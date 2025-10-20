import { Button } from '@/components/ui/button';
import { DatePicker } from '@/components/ui/date-picker';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { update } from '@/routes/bills';
import { Form } from '@inertiajs/react';
import { PencilIcon } from 'lucide-react';
import { useState } from 'react';

type Bill = {
    id: number;
    name: string;
    amount: number;
    due_date: string;
};

type Props = {
    bill: Bill;
};

export default function EditBillDialog({ bill }: Props) {
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <button
                    className="text-muted-foreground transition-colors hover:text-primary"
                    aria-label="Edit bill"
                >
                    <PencilIcon className="size-4" />
                </button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Edit Bill</DialogTitle>
                    <DialogDescription>
                        Update the bill details.
                    </DialogDescription>
                </DialogHeader>

                <Form
                    action={update({ bill: bill.id }).url}
                    method="put"
                    onSuccess={() => setOpen(false)}
                >
                    {({ errors, processing }) => (
                        <>
                            <div className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="name">Bill Name</Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        placeholder="e.g., Rent, Electric, Internet"
                                        required
                                        defaultValue={bill.name}
                                    />
                                    {errors.name && (
                                        <p className="text-sm text-red-600">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="amount">Amount</Label>
                                    <Input
                                        id="amount"
                                        name="amount"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        placeholder="0.00"
                                        required
                                        defaultValue={bill.amount}
                                    />
                                    {errors.amount && (
                                        <p className="text-sm text-red-600">
                                            {errors.amount}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="due_date">Due Date</Label>
                                    <DatePicker
                                        id="due_date"
                                        name="due_date"
                                        defaultValue={bill.due_date}
                                        required
                                    />
                                    {errors.due_date && (
                                        <p className="text-sm text-red-600">
                                            {errors.due_date}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <DialogFooter className="mt-6">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => setOpen(false)}
                                    disabled={processing}
                                >
                                    Cancel
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Saving...' : 'Save Changes'}
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
