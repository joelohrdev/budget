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
import { Textarea } from '@/components/ui/textarea';
import { store as storePayment } from '@/routes/debts/payments';
import { Form } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';
import { useState } from 'react';

type Props = {
    debtId: number;
    trigger?: React.ReactNode;
};

export default function AddPaymentDialog({ debtId, trigger }: Props) {
    const [open, setOpen] = useState(false);
    const [paymentAmount, setPaymentAmount] = useState('');
    const [interestAmount, setInterestAmount] = useState('');
    const today = new Date().toISOString().split('T')[0];

    const calculatePrincipal = () => {
        const payment = parseFloat(paymentAmount) || 0;
        const interest = parseFloat(interestAmount) || 0;
        return Math.max(0, payment - interest).toFixed(2);
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                {trigger || (
                    <Button>
                        <PlusIcon className="size-4" />
                        Add Payment
                    </Button>
                )}
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Record Payment</DialogTitle>
                    <DialogDescription>
                        Add a payment you made toward this debt
                    </DialogDescription>
                </DialogHeader>

                <Form
                    action={storePayment({ debt: debtId }).url}
                    method="post"
                    resetOnSuccess
                    onSuccess={() => {
                        setOpen(false);
                        setPaymentAmount('');
                        setInterestAmount('');
                    }}
                >
                    {({ errors, processing }) => (
                        <>
                            <div className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="payment_date">Payment Date</Label>
                                    <DatePicker
                                        id="payment_date"
                                        name="payment_date"
                                        defaultValue={today}
                                        required
                                    />
                                    {errors.payment_date && (
                                        <p className="text-sm text-red-600">
                                            {errors.payment_date}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="amount">Total Payment Amount</Label>
                                    <Input
                                        id="amount"
                                        name="amount"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        placeholder="0.00"
                                        required
                                        value={paymentAmount}
                                        onChange={(e) => setPaymentAmount(e.target.value)}
                                    />
                                    {errors.amount && (
                                        <p className="text-sm text-red-600">
                                            {errors.amount}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="interest_amount">
                                        Interest Portion
                                    </Label>
                                    <Input
                                        id="interest_amount"
                                        name="interest_amount"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        required
                                        value={interestAmount}
                                        onChange={(e) => setInterestAmount(e.target.value)}
                                    />
                                    {errors.interest_amount && (
                                        <p className="text-sm text-red-600">
                                            {errors.interest_amount}
                                        </p>
                                    )}
                                    <p className="text-xs text-muted-foreground">
                                        The amount of your payment that went to interest
                                    </p>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="principal_amount">
                                        Principal Portion
                                    </Label>
                                    <Input
                                        id="principal_amount"
                                        name="principal_amount"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        required
                                        value={calculatePrincipal()}
                                        readOnly
                                        className="bg-muted"
                                    />
                                    {errors.principal_amount && (
                                        <p className="text-sm text-red-600">
                                            {errors.principal_amount}
                                        </p>
                                    )}
                                    <p className="text-xs text-muted-foreground">
                                        Automatically calculated: Payment - Interest
                                    </p>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="notes">Notes (Optional)</Label>
                                    <Textarea
                                        id="notes"
                                        name="notes"
                                        placeholder="Additional notes about this payment"
                                        rows={2}
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
                                    {processing ? 'Recording...' : 'Record Payment'}
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
