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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { update } from '@/routes/debts';
import { Form } from '@inertiajs/react';
import { useState } from 'react';

type Debt = {
    id: number;
    name: string;
    type: string;
    principal_amount: number;
    current_balance: number;
    interest_rate: number;
    minimum_payment: number | null;
    term_months: number | null;
    start_date: string;
    payoff_target_date: string | null;
    notes: string | null;
};

type Props = {
    debt: Debt;
    trigger?: React.ReactNode;
};

export default function EditDebtDialog({ debt, trigger }: Props) {
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                {trigger || <Button>Edit Debt</Button>}
            </DialogTrigger>
            <DialogContent className="max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Edit Debt</DialogTitle>
                    <DialogDescription>
                        Update the debt details
                    </DialogDescription>
                </DialogHeader>

                <Form
                    action={update({ debt: debt.id }).url}
                    method="put"
                    onSuccess={() => setOpen(false)}
                >
                    {({ errors, processing }) => (
                        <>
                            <div className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="name">Debt Name</Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        placeholder="e.g., Chase Credit Card, Auto Loan"
                                        required
                                        defaultValue={debt.name}
                                    />
                                    {errors.name && (
                                        <p className="text-sm text-red-600">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="type">Debt Type</Label>
                                    <Select name="type" defaultValue={debt.type} required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="credit_card">
                                                Credit Card
                                            </SelectItem>
                                            <SelectItem value="loan">Loan</SelectItem>
                                            <SelectItem value="mortgage">
                                                Mortgage
                                            </SelectItem>
                                            <SelectItem value="other">Other</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.type && (
                                        <p className="text-sm text-red-600">
                                            {errors.type}
                                        </p>
                                    )}
                                </div>

                                <div className="grid gap-4 sm:grid-cols-2">
                                    <div className="space-y-2">
                                        <Label htmlFor="principal_amount">
                                            Original Loan Amount (Optional)
                                        </Label>
                                        <Input
                                            id="principal_amount"
                                            name="principal_amount"
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            placeholder="0.00"
                                            defaultValue={debt.principal_amount}
                                        />
                                        {errors.principal_amount && (
                                            <p className="text-sm text-red-600">
                                                {errors.principal_amount}
                                            </p>
                                        )}
                                        <p className="text-xs text-muted-foreground">
                                            For loans/mortgages: the amount you originally borrowed. Leave blank for credit cards.
                                        </p>
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="current_balance">
                                            Current Balance Owed
                                        </Label>
                                        <Input
                                            id="current_balance"
                                            name="current_balance"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="0.00"
                                            required
                                            defaultValue={debt.current_balance}
                                        />
                                        {errors.current_balance && (
                                            <p className="text-sm text-red-600">
                                                {errors.current_balance}
                                            </p>
                                        )}
                                        <p className="text-xs text-muted-foreground">
                                            How much you owe right now
                                        </p>
                                    </div>
                                </div>

                                <div className="grid gap-4 sm:grid-cols-2">
                                    <div className="space-y-2">
                                        <Label htmlFor="interest_rate">
                                            Interest Rate (%)
                                        </Label>
                                        <Input
                                            id="interest_rate"
                                            name="interest_rate"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            placeholder="0.00"
                                            required
                                            defaultValue={debt.interest_rate}
                                        />
                                        {errors.interest_rate && (
                                            <p className="text-sm text-red-600">
                                                {errors.interest_rate}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="minimum_payment">
                                            Minimum Payment (Optional)
                                        </Label>
                                        <Input
                                            id="minimum_payment"
                                            name="minimum_payment"
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            placeholder="0.00"
                                            defaultValue={debt.minimum_payment || ''}
                                        />
                                        {errors.minimum_payment && (
                                            <p className="text-sm text-red-600">
                                                {errors.minimum_payment}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                <div className="grid gap-4 sm:grid-cols-2">
                                    <div className="space-y-2">
                                        <Label htmlFor="start_date">Start Date</Label>
                                        <DatePicker
                                            id="start_date"
                                            name="start_date"
                                            defaultValue={debt.start_date}
                                            required
                                        />
                                        {errors.start_date && (
                                            <p className="text-sm text-red-600">
                                                {errors.start_date}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="payoff_target_date">
                                            Target Payoff Date (Optional)
                                        </Label>
                                        <DatePicker
                                            id="payoff_target_date"
                                            name="payoff_target_date"
                                            defaultValue={debt.payoff_target_date || ''}
                                        />
                                        {errors.payoff_target_date && (
                                            <p className="text-sm text-red-600">
                                                {errors.payoff_target_date}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="term_months">
                                        Term (Months) (Optional)
                                    </Label>
                                    <Input
                                        id="term_months"
                                        name="term_months"
                                        type="number"
                                        min="1"
                                        placeholder="e.g., 60 for 5 years"
                                        defaultValue={debt.term_months || ''}
                                    />
                                    {errors.term_months && (
                                        <p className="text-sm text-red-600">
                                            {errors.term_months}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="notes">Notes (Optional)</Label>
                                    <Textarea
                                        id="notes"
                                        name="notes"
                                        placeholder="Additional notes about this debt"
                                        rows={3}
                                        defaultValue={debt.notes || ''}
                                    />
                                    {errors.notes && (
                                        <p className="text-sm text-red-600">
                                            {errors.notes}
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
