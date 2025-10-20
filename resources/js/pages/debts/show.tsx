import AddPaymentDialog from '@/components/add-payment-dialog';
import EditDebtDialog from '@/components/edit-debt-dialog';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { index as debtsIndex, destroy } from '@/routes/debts';
import { destroy as destroyPayment } from '@/routes/debts/payments';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeftIcon, PlusIcon, TrashIcon } from 'lucide-react';

type Debt = {
    id: number;
    name: string;
    type: string;
    principal_amount: number | null;
    current_balance: number;
    interest_rate: number;
    minimum_payment: number | null;
    term_months: number | null;
    start_date: string;
    payoff_target_date: string | null;
    notes: string | null;
    monthly_interest: number;
};

type Payment = {
    id: number;
    amount: number;
    principal_amount: number;
    interest_amount: number;
    payment_date: string;
    notes: string | null;
};

type Props = {
    debt: Debt;
    payments: Payment[];
};

export default function DebtShow({ debt, payments }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Debts',
            href: debtsIndex().url,
        },
        {
            title: debt.name,
            href: `/debts/${debt.id}`,
        },
    ];

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

    const getDebtTypeLabel = (type: string) => {
        const labels: Record<string, string> = {
            credit_card: 'Credit Card',
            loan: 'Loan',
            mortgage: 'Mortgage',
            other: 'Other',
        };
        return labels[type] || type;
    };

    const totalPaid = payments.reduce((sum, p) => sum + p.principal_amount, 0);
    const totalInterestPaid = payments.reduce((sum, p) => sum + p.interest_amount, 0);
    const progressPercentage = debt.principal_amount && debt.principal_amount > 0
        ? ((debt.principal_amount - debt.current_balance) / debt.principal_amount) * 100
        : null;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${debt.name} - Debts`} />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex items-center gap-3">
                        <Link href={debtsIndex().url}>
                            <Button variant="outline" size="icon">
                                <ArrowLeftIcon className="size-4" />
                            </Button>
                        </Link>
                        <Heading
                            title={debt.name}
                            description={getDebtTypeLabel(debt.type)}
                        />
                    </div>
                    <div className="flex gap-2">
                        <EditDebtDialog debt={debt} />
                        <Button
                            variant="outline"
                            onClick={() => {
                                if (
                                    confirm(
                                        'Are you sure you want to delete this debt? This will also delete all payment records.',
                                    )
                                ) {
                                    router.delete(destroy({ debt: debt.id }).url);
                                }
                            }}
                        >
                            Delete
                        </Button>
                    </div>
                </div>

                <div className={`grid gap-4 sm:grid-cols-2 ${debt.principal_amount ? 'lg:grid-cols-4' : 'lg:grid-cols-3'}`}>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Current Balance</CardDescription>
                            <CardTitle className="text-3xl">
                                {formatCurrency(debt.current_balance)}
                            </CardTitle>
                        </CardHeader>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Interest Rate</CardDescription>
                            <CardTitle className="text-3xl">
                                {debt.interest_rate}%
                            </CardTitle>
                        </CardHeader>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Monthly Interest</CardDescription>
                            <CardTitle className="text-3xl">
                                {formatCurrency(debt.monthly_interest)}
                            </CardTitle>
                        </CardHeader>
                    </Card>
                    {debt.principal_amount && (
                        <Card>
                            <CardHeader className="pb-2">
                                <CardDescription>Original Loan Amount</CardDescription>
                                <CardTitle className="text-3xl">
                                    {formatCurrency(debt.principal_amount)}
                                </CardTitle>
                            </CardHeader>
                        </Card>
                    )}
                </div>

                <div className="grid gap-4 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Debt Details</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Start Date</span>
                                <span className="font-medium">{formatDate(debt.start_date)}</span>
                            </div>
                            {debt.payoff_target_date && (
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Target Payoff</span>
                                    <span className="font-medium">{formatDate(debt.payoff_target_date)}</span>
                                </div>
                            )}
                            {debt.minimum_payment && (
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Minimum Payment</span>
                                    <span className="font-medium">{formatCurrency(debt.minimum_payment)}</span>
                                </div>
                            )}
                            {debt.term_months && (
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Term</span>
                                    <span className="font-medium">{debt.term_months} months</span>
                                </div>
                            )}
                            {debt.notes && (
                                <div className="pt-2">
                                    <p className="text-sm text-muted-foreground mb-1">Notes</p>
                                    <p className="text-sm">{debt.notes}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Payment Summary</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Total Payments</span>
                                <span className="font-medium">{payments.length}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Principal Paid</span>
                                <span className="font-medium">{formatCurrency(totalPaid)}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Interest Paid</span>
                                <span className="font-medium">{formatCurrency(totalInterestPaid)}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Remaining Balance</span>
                                <span className="font-bold text-lg">{formatCurrency(debt.current_balance)}</span>
                            </div>
                            {progressPercentage !== null && (
                                <div className="flex justify-between pt-2 border-t">
                                    <span className="text-muted-foreground">Progress</span>
                                    <span className="font-bold">{progressPercentage.toFixed(1)}%</span>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <CardTitle>Payment History</CardTitle>
                            <AddPaymentDialog debtId={debt.id} />
                        </div>
                        <CardDescription>
                            Record of all payments made toward this debt
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {payments.length === 0 ? (
                            <div className="text-center py-8 text-muted-foreground">
                                No payments recorded yet
                            </div>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Date</TableHead>
                                        <TableHead className="text-right">Amount</TableHead>
                                        <TableHead className="text-right">Principal</TableHead>
                                        <TableHead className="text-right">Interest</TableHead>
                                        <TableHead>Notes</TableHead>
                                        <TableHead className="w-[50px]"></TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {payments.map((payment) => (
                                        <TableRow key={payment.id}>
                                            <TableCell className="font-medium">
                                                {formatDate(payment.payment_date)}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {formatCurrency(payment.amount)}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {formatCurrency(payment.principal_amount)}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {formatCurrency(payment.interest_amount)}
                                            </TableCell>
                                            <TableCell className="text-muted-foreground">
                                                {payment.notes || '-'}
                                            </TableCell>
                                            <TableCell>
                                                <button
                                                    onClick={() => {
                                                        if (
                                                            confirm(
                                                                'Are you sure you want to delete this payment?',
                                                            )
                                                        ) {
                                                            router.delete(
                                                                destroyPayment({
                                                                    debt: debt.id,
                                                                    payment: payment.id,
                                                                }).url,
                                                            );
                                                        }
                                                    }}
                                                    className="text-muted-foreground transition-colors hover:text-red-600"
                                                    aria-label="Delete payment"
                                                >
                                                    <TrashIcon className="size-4" />
                                                </button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
