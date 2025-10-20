import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { CalculatorIcon, TrendingDownIcon, TrendingUpIcon } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Debt Calculator',
        href: '/debt-calculator',
    },
];

type Debt = {
    id: number;
    name: string;
    type: string;
    current_balance: number;
    interest_rate: number;
    minimum_payment: number | null;
    monthly_interest: number;
};

type StrategyResult = {
    strategy: string;
    total_months: number;
    total_interest: number;
    timeline: {
        month: number;
        payments: {
            debt_id: number;
            debt_name: string;
            payment: number;
            principal: number;
            interest: number;
            remaining_balance: number;
        }[];
    }[];
};

type Props = {
    debts: Debt[];
};

export default function DebtCalculator({ debts }: Props) {
    const [extraPayment, setExtraPayment] = useState('0');
    const [snowballResult, setSnowballResult] = useState<StrategyResult | null>(null);
    const [avalancheResult, setAvalancheResult] = useState<StrategyResult | null>(null);
    const [loading, setLoading] = useState(false);

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(amount);
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

    const totalDebt = debts.reduce((sum, debt) => sum + debt.current_balance, 0);
    const totalMinimumPayments = debts.reduce(
        (sum, debt) => sum + (debt.minimum_payment || 0),
        0
    );
    const totalMonthlyInterest = debts.reduce(
        (sum, debt) => sum + debt.monthly_interest,
        0
    );

    const calculateStrategies = async () => {
        setLoading(true);
        const payment = parseFloat(extraPayment);

        if (isNaN(payment) || payment < 0) {
            setLoading(false);
            return;
        }

        try {
            const [snowballResponse, avalancheResponse] = await Promise.all([
                window.axios.post('/debt-calculator/snowball', {
                    extra_payment: payment,
                }),
                window.axios.post('/debt-calculator/avalanche', {
                    extra_payment: payment,
                }),
            ]);

            setSnowballResult(snowballResponse.data);
            setAvalancheResult(avalancheResponse.data);
        } catch (error) {
            console.error('Error calculating strategies:', error);
        } finally {
            setLoading(false);
        }
    };

    const formatMonths = (months: number) => {
        const years = Math.floor(months / 12);
        const remainingMonths = months % 12;

        if (years === 0) {
            return `${months} month${months !== 1 ? 's' : ''}`;
        } else if (remainingMonths === 0) {
            return `${years} year${years !== 1 ? 's' : ''}`;
        } else {
            return `${years} year${years !== 1 ? 's' : ''}, ${remainingMonths} month${remainingMonths !== 1 ? 's' : ''}`;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Debt Calculator" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <Heading
                    title="Debt Payoff Calculator"
                    description="Compare debt snowball and avalanche strategies"
                />

                {debts.length === 0 && (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <CalculatorIcon className="mb-4 size-12 text-muted-foreground" />
                            <p className="mb-2 text-lg font-medium">
                                No debts to calculate
                            </p>
                            <p className="mb-4 text-sm text-muted-foreground">
                                Add some debts first to use the payoff calculator
                            </p>
                        </CardContent>
                    </Card>
                )}

                {debts.length > 0 && (
                    <>
                        <div className="grid gap-4 sm:grid-cols-3">
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardDescription>Total Debt</CardDescription>
                                    <CardTitle className="text-3xl">
                                        {formatCurrency(totalDebt)}
                                    </CardTitle>
                                </CardHeader>
                            </Card>
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardDescription>Minimum Payments</CardDescription>
                                    <CardTitle className="text-3xl">
                                        {formatCurrency(totalMinimumPayments)}
                                    </CardTitle>
                                </CardHeader>
                            </Card>
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardDescription>Monthly Interest</CardDescription>
                                    <CardTitle className="text-3xl">
                                        {formatCurrency(totalMonthlyInterest)}
                                    </CardTitle>
                                </CardHeader>
                            </Card>
                        </div>

                        <Card>
                            <CardHeader>
                                <CardTitle>Your Debts</CardTitle>
                                <CardDescription>
                                    Overview of all debts included in the calculation
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    {debts.map((debt) => (
                                        <div
                                            key={debt.id}
                                            className="flex items-center justify-between border-b pb-3 last:border-0"
                                        >
                                            <div>
                                                <p className="font-medium">{debt.name}</p>
                                                <p className="text-sm text-muted-foreground">
                                                    {getDebtTypeLabel(debt.type)} · {debt.interest_rate}% APR
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <p className="font-bold">
                                                    {formatCurrency(debt.current_balance)}
                                                </p>
                                                {debt.minimum_payment && (
                                                    <p className="text-sm text-muted-foreground">
                                                        Min: {formatCurrency(debt.minimum_payment)}
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Calculate Payoff Strategy</CardTitle>
                                <CardDescription>
                                    Enter your extra monthly payment to compare strategies
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                <div className="space-y-2">
                                    <Label htmlFor="extra_payment">
                                        Extra Monthly Payment
                                    </Label>
                                    <Input
                                        id="extra_payment"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={extraPayment}
                                        onChange={(e) => setExtraPayment(e.target.value)}
                                        placeholder="0.00"
                                    />
                                    <p className="text-xs text-muted-foreground">
                                        Amount you can pay above minimum payments each month
                                    </p>
                                </div>

                                <Button
                                    onClick={calculateStrategies}
                                    disabled={loading || parseFloat(extraPayment) < 0}
                                    className="w-full"
                                >
                                    {loading ? 'Calculating...' : 'Compare Strategies'}
                                </Button>
                            </CardContent>
                        </Card>

                        {snowballResult && avalancheResult && (
                            <>
                                <div className="grid gap-4 lg:grid-cols-2">
                                    <Card>
                                        <CardHeader>
                                            <div className="flex items-center justify-between">
                                                <CardTitle className="flex items-center gap-2">
                                                    <TrendingDownIcon className="size-5 text-blue-500" />
                                                    Debt Snowball
                                                </CardTitle>
                                            </div>
                                            <CardDescription>
                                                Pay off smallest balances first
                                            </CardDescription>
                                        </CardHeader>
                                        <CardContent className="space-y-4">
                                            <div className="grid grid-cols-2 gap-4">
                                                <div>
                                                    <p className="text-sm text-muted-foreground">
                                                        Payoff Time
                                                    </p>
                                                    <p className="text-2xl font-bold">
                                                        {formatMonths(snowballResult.total_months)}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p className="text-sm text-muted-foreground">
                                                        Total Interest
                                                    </p>
                                                    <p className="text-2xl font-bold">
                                                        {formatCurrency(snowballResult.total_interest)}
                                                    </p>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>

                                    <Card>
                                        <CardHeader>
                                            <div className="flex items-center justify-between">
                                                <CardTitle className="flex items-center gap-2">
                                                    <TrendingUpIcon className="size-5 text-green-500" />
                                                    Debt Avalanche
                                                </CardTitle>
                                            </div>
                                            <CardDescription>
                                                Pay off highest interest rates first
                                            </CardDescription>
                                        </CardHeader>
                                        <CardContent className="space-y-4">
                                            <div className="grid grid-cols-2 gap-4">
                                                <div>
                                                    <p className="text-sm text-muted-foreground">
                                                        Payoff Time
                                                    </p>
                                                    <p className="text-2xl font-bold">
                                                        {formatMonths(avalancheResult.total_months)}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p className="text-sm text-muted-foreground">
                                                        Total Interest
                                                    </p>
                                                    <p className="text-2xl font-bold">
                                                        {formatCurrency(avalancheResult.total_interest)}
                                                    </p>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </div>

                                {avalancheResult.total_interest < snowballResult.total_interest && (
                                    <Card className="border-green-500 bg-green-50 dark:bg-green-950">
                                        <CardContent className="flex items-center gap-3 py-4">
                                            <TrendingUpIcon className="size-5 text-green-600 dark:text-green-400" />
                                            <div>
                                                <p className="font-semibold text-green-900 dark:text-green-100">
                                                    Avalanche saves you {formatCurrency(snowballResult.total_interest - avalancheResult.total_interest)} in interest
                                                </p>
                                                <p className="text-sm text-green-700 dark:text-green-300">
                                                    By focusing on high-interest debts first, you'll pay less over time
                                                </p>
                                            </div>
                                        </CardContent>
                                    </Card>
                                )}

                                <Card>
                                    <CardHeader>
                                        <CardTitle>First 12 Months Timeline</CardTitle>
                                        <CardDescription>
                                            Month-by-month breakdown of both strategies
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="space-y-6">
                                            <div>
                                                <h4 className="mb-3 font-semibold">Snowball Strategy</h4>
                                                <div className="overflow-x-auto">
                                                    <Table>
                                                        <TableHeader>
                                                            <TableRow>
                                                                <TableHead>Month</TableHead>
                                                                <TableHead>Debt</TableHead>
                                                                <TableHead className="text-right">Payment</TableHead>
                                                                <TableHead className="text-right">Principal</TableHead>
                                                                <TableHead className="text-right">Interest</TableHead>
                                                                <TableHead className="text-right">Balance</TableHead>
                                                            </TableRow>
                                                        </TableHeader>
                                                        <TableBody>
                                                            {snowballResult.timeline.map((monthData) =>
                                                                monthData.payments.map((payment, idx) => (
                                                                    <TableRow key={`snowball-${monthData.month}-${idx}`}>
                                                                        {idx === 0 && (
                                                                            <TableCell rowSpan={monthData.payments.length} className="font-medium">
                                                                                {monthData.month}
                                                                            </TableCell>
                                                                        )}
                                                                        <TableCell className="text-sm">{payment.debt_name}</TableCell>
                                                                        <TableCell className="text-right">{formatCurrency(payment.payment)}</TableCell>
                                                                        <TableCell className="text-right">{formatCurrency(payment.principal)}</TableCell>
                                                                        <TableCell className="text-right">{formatCurrency(payment.interest)}</TableCell>
                                                                        <TableCell className="text-right font-medium">
                                                                            {formatCurrency(payment.remaining_balance)}
                                                                        </TableCell>
                                                                    </TableRow>
                                                                ))
                                                            )}
                                                        </TableBody>
                                                    </Table>
                                                </div>
                                            </div>

                                            <div>
                                                <h4 className="mb-3 font-semibold">Avalanche Strategy</h4>
                                                <div className="overflow-x-auto">
                                                    <Table>
                                                        <TableHeader>
                                                            <TableRow>
                                                                <TableHead>Month</TableHead>
                                                                <TableHead>Debt</TableHead>
                                                                <TableHead className="text-right">Payment</TableHead>
                                                                <TableHead className="text-right">Principal</TableHead>
                                                                <TableHead className="text-right">Interest</TableHead>
                                                                <TableHead className="text-right">Balance</TableHead>
                                                            </TableRow>
                                                        </TableHeader>
                                                        <TableBody>
                                                            {avalancheResult.timeline.map((monthData) =>
                                                                monthData.payments.map((payment, idx) => (
                                                                    <TableRow key={`avalanche-${monthData.month}-${idx}`}>
                                                                        {idx === 0 && (
                                                                            <TableCell rowSpan={monthData.payments.length} className="font-medium">
                                                                                {monthData.month}
                                                                            </TableCell>
                                                                        )}
                                                                        <TableCell className="text-sm">{payment.debt_name}</TableCell>
                                                                        <TableCell className="text-right">{formatCurrency(payment.payment)}</TableCell>
                                                                        <TableCell className="text-right">{formatCurrency(payment.principal)}</TableCell>
                                                                        <TableCell className="text-right">{formatCurrency(payment.interest)}</TableCell>
                                                                        <TableCell className="text-right font-medium">
                                                                            {formatCurrency(payment.remaining_balance)}
                                                                        </TableCell>
                                                                    </TableRow>
                                                                ))
                                                            )}
                                                        </TableBody>
                                                    </Table>
                                                </div>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </>
                        )}
                    </>
                )}
            </div>
        </AppLayout>
    );
}
