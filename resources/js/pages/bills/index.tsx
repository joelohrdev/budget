import EditBillDialog from '@/components/edit-bill-dialog';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
import AppLayout from '@/layouts/app-layout';
import { store, destroy } from '@/routes/bills';
import { type BreadcrumbItem } from '@/types';
import { Form } from '@inertiajs/react';
import { Head, router } from '@inertiajs/react';
import { CalendarIcon, DollarSignIcon, PlusIcon, TrashIcon } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Bills',
        href: '/bills',
    },
];

type Bill = {
    id: number;
    name: string;
    amount: number;
    due_date: string;
};

type Props = {
    bills: Bill[];
};

export default function BillsIndex({ bills }: Props) {
    const [isOpen, setIsOpen] = useState(false);

    const today = new Date().toISOString().split('T')[0];

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(amount);
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Bills" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Recurring Bills"
                        description="Manage your monthly recurring bills"
                    />
                    <Dialog open={isOpen} onOpenChange={setIsOpen}>
                        <DialogTrigger asChild>
                            <Button>
                                <PlusIcon className="size-4" />
                                Add Bill
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Add New Bill</DialogTitle>
                                <DialogDescription>
                                    Add a recurring bill to track each month
                                </DialogDescription>
                            </DialogHeader>
                            <Form
                                action={store().url}
                                method="post"
                                resetOnSuccess
                            >
                                {({ errors, processing }) => (
                                    <>
                                        <div className="space-y-4">
                                            <div className="space-y-2">
                                                <Label htmlFor="name">
                                                    Bill Name
                                                </Label>
                                                <Input
                                                    id="name"
                                                    name="name"
                                                    placeholder="e.g., Rent, Electric, Internet"
                                                    required
                                                />
                                                {errors.name && (
                                                    <p className="text-sm text-red-600">
                                                        {errors.name}
                                                    </p>
                                                )}
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="amount">
                                                    Amount
                                                </Label>
                                                <Input
                                                    id="amount"
                                                    name="amount"
                                                    type="number"
                                                    step="0.01"
                                                    min="0.01"
                                                    placeholder="0.00"
                                                    required
                                                />
                                                {errors.amount && (
                                                    <p className="text-sm text-red-600">
                                                        {errors.amount}
                                                    </p>
                                                )}
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="due_date">
                                                    Due Date
                                                </Label>
                                                <DatePicker
                                                    id="due_date"
                                                    name="due_date"
                                                    defaultValue={today}
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
                                                onClick={() => setIsOpen(false)}
                                                disabled={processing}
                                            >
                                                Cancel
                                            </Button>
                                            <Button
                                                type="submit"
                                                disabled={processing}
                                            >
                                                {processing
                                                    ? 'Adding...'
                                                    : 'Add Bill'}
                                            </Button>
                                        </DialogFooter>
                                    </>
                                )}
                            </Form>
                        </DialogContent>
                    </Dialog>
                </div>

                {bills.length === 0 && (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <DollarSignIcon className="mb-4 size-12 text-muted-foreground" />
                            <p className="mb-2 text-lg font-medium">
                                No bills yet
                            </p>
                            <p className="mb-4 text-sm text-muted-foreground">
                                Add your first recurring bill to start tracking
                            </p>
                            <Button onClick={() => setIsOpen(true)}>
                                <PlusIcon className="size-4" />
                                Add Bill
                            </Button>
                        </CardContent>
                    </Card>
                )}

                {bills.length > 0 && (
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {bills.map((bill) => (
                            <Card key={bill.id}>
                                <CardHeader>
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <CardTitle className="text-lg">
                                                {bill.name}
                                            </CardTitle>
                                            <CardDescription className="mt-1 flex items-center gap-1">
                                                <CalendarIcon className="size-3" />
                                                Due {formatDate(bill.due_date)}
                                            </CardDescription>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <EditBillDialog bill={bill} />
                                            <button
                                                onClick={() => {
                                                    if (
                                                        confirm(
                                                            'Are you sure you want to delete this bill?',
                                                        )
                                                    ) {
                                                        router.delete(
                                                            destroy({ bill: bill.id }).url,
                                                        );
                                                    }
                                                }}
                                                className="text-muted-foreground transition-colors hover:text-red-600"
                                                aria-label="Delete bill"
                                            >
                                                <TrashIcon className="size-4" />
                                            </button>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex items-baseline gap-1">
                                        <span className="text-3xl font-bold">
                                            {formatCurrency(bill.amount)}
                                        </span>
                                        <span className="text-sm text-muted-foreground">
                                            /month
                                        </span>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
