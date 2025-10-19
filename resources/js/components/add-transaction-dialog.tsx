import TransactionsController from '@/actions/App/Http/Controllers/TransactionsController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { DatePicker } from '@/components/ui/date-picker';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, router } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';
import { useState } from 'react';

type Card = {
    id: number;
    name: string;
    type: string;
};

type Category = {
    id: number;
    name: string;
    color: string | null;
};

type Props = {
    cards: Card[];
    categories?: Category[];
};

export default function AddTransactionDialog({ cards, categories = [] }: Props) {
    const [open, setOpen] = useState(false);
    const [showNewCategory, setShowNewCategory] = useState(false);
    const [newCategoryName, setNewCategoryName] = useState('');
    const [creatingCategory, setCreatingCategory] = useState(false);

    const today = new Date().toISOString().split('T')[0];

    const handleCreateCategory = (e: React.FormEvent) => {
        e.preventDefault();

        if (!newCategoryName.trim()) {
            return;
        }

        setCreatingCategory(true);

        router.post(
            '/categories',
            {
                name: newCategoryName,
                color: '#6b7280',
            },
            {
                preserveScroll: true,
                preserveState: true,
                only: ['categories'],
                onSuccess: () => {
                    setNewCategoryName('');
                    setShowNewCategory(false);
                },
                onError: (errors) => {
                    console.error('Failed to create category:', errors);
                },
                onFinish: () => {
                    setCreatingCategory(false);
                },
            },
        );
    };

    return (
        <Dialog
            open={open}
            onOpenChange={(isOpen) => {
                setOpen(isOpen);
                if (!isOpen) {
                    setShowNewCategory(false);
                    setNewCategoryName('');
                }
            }}
        >
            <DialogTrigger asChild>
                <Button>
                    <PlusIcon className="size-4" />
                    Add Transaction
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Add Transaction</DialogTitle>
                    <DialogDescription>
                        Record a new transaction or credit for your cards.
                    </DialogDescription>
                </DialogHeader>

                <Form
                    {...TransactionsController.store.form()}
                    options={{
                        onSuccess: () => setOpen(false),
                    }}
                    className="space-y-4"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="card_id">Card</Label>
                                <select
                                    id="card_id"
                                    name="card_id"
                                    className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                    required
                                >
                                    <option value="">Select a card</option>
                                    {cards.map((card) => (
                                        <option key={card.id} value={card.id}>
                                            {card.name} ({card.type})
                                        </option>
                                    ))}
                                </select>
                                <InputError message={errors.card_id} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="category_id">Category</Label>
                                {showNewCategory ? (
                                    <div className="flex gap-2">
                                        <Input
                                            value={newCategoryName}
                                            onChange={(e) =>
                                                setNewCategoryName(
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="New category name"
                                            disabled={creatingCategory}
                                        />
                                        <Button
                                            type="button"
                                            onClick={(e) => handleCreateCategory(e)}
                                            disabled={
                                                creatingCategory ||
                                                !newCategoryName.trim()
                                            }
                                        >
                                            Save
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() => {
                                                setShowNewCategory(false);
                                                setNewCategoryName('');
                                            }}
                                            disabled={creatingCategory}
                                        >
                                            Cancel
                                        </Button>
                                    </div>
                                ) : (
                                    <div className="flex gap-2">
                                        <select
                                            id="category_id"
                                            name="category_id"
                                            className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                        >
                                            <option value="">
                                                No category
                                            </option>
                                            {categories.map((category) => (
                                                <option
                                                    key={category.id}
                                                    value={category.id}
                                                >
                                                    {category.name}
                                                </option>
                                            ))}
                                        </select>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() =>
                                                setShowNewCategory(true)
                                            }
                                        >
                                            <PlusIcon className="size-4" />
                                        </Button>
                                    </div>
                                )}
                                <InputError message={errors.category_id} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="description">Description</Label>
                                <Input
                                    id="description"
                                    name="description"
                                    placeholder="e.g., Grocery Store"
                                    required
                                />
                                <InputError message={errors.description} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="amount">Amount</Label>
                                <Input
                                    id="amount"
                                    name="amount"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    placeholder="0.00"
                                    required
                                />
                                <InputError message={errors.amount} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="type">Type</Label>
                                <select
                                    id="type"
                                    name="type"
                                    className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                    defaultValue="debit"
                                    required
                                >
                                    <option value="debit">Debit (Expense)</option>
                                    <option value="credit">Credit (Refund)</option>
                                </select>
                                <InputError message={errors.type} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="transaction_date">Date</Label>
                                <DatePicker
                                    id="transaction_date"
                                    name="transaction_date"
                                    defaultValue={today}
                                    required
                                />
                                <InputError message={errors.transaction_date} />
                            </div>

                            <div className="flex gap-4">
                                <Button type="submit" disabled={processing}>
                                    Add Transaction
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => setOpen(false)}
                                >
                                    Cancel
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
