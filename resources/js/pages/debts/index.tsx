import AddDebtDialog from '@/components/add-debt-dialog';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import AppLayout from '@/layouts/app-layout';
import { show } from '@/routes/debts';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { CreditCardIcon, PlusIcon } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Debts',
        href: '/debts',
    },
];

type Debt = {
    id: number;
    name: string;
    type: string;
    principal_amount: number | null;
    current_balance: number;
    interest_rate: number;
    minimum_payment: number | null;
    monthly_interest: number;
    progress_percentage: number | null;
    total_paid: number;
    total_interest_paid: number;
};

type Summary = {
    total_debt: number;
    total_monthly_interest: number;
    total_principal: number;
    total_paid: number;
};

type Props = {
    debts: Debt[];
    summary: Summary;
};

export default function DebtsIndex({ debts, summary }: Props) {
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

    const getDebtTypeColor = (type: string) => {
        const colors: Record<string, string> = {
            credit_card: 'bg-blue-500',
            loan: 'bg-green-500',
            mortgage: 'bg-purple-500',
            other: 'bg-gray-500',
        };
        return colors[type] || 'bg-gray-500';
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Debts" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Debt Tracking"
                        description="Track and manage your debts"
                    />
                    <AddDebtDialog />
                </div>

                {debts.length > 0 && (
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardDescription>Total Debt</CardDescription>
                                <CardTitle className="text-3xl">
                                    {formatCurrency(summary.total_debt)}
                                </CardTitle>
                            </CardHeader>
                        </Card>
                        <Card>
                            <CardHeader className="pb-2">
                                <CardDescription>Monthly Interest</CardDescription>
                                <CardTitle className="text-3xl">
                                    {formatCurrency(summary.total_monthly_interest)}
                                </CardTitle>
                            </CardHeader>
                        </Card>
                        <Card>
                            <CardHeader className="pb-2">
                                <CardDescription>Total Principal</CardDescription>
                                <CardTitle className="text-3xl">
                                    {formatCurrency(summary.total_principal)}
                                </CardTitle>
                            </CardHeader>
                        </Card>
                        <Card>
                            <CardHeader className="pb-2">
                                <CardDescription>Total Paid</CardDescription>
                                <CardTitle className="text-3xl">
                                    {formatCurrency(summary.total_paid)}
                                </CardTitle>
                            </CardHeader>
                        </Card>
                    </div>
                )}

                {debts.length === 0 && (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <CreditCardIcon className="mb-4 size-12 text-muted-foreground" />
                            <p className="mb-2 text-lg font-medium">
                                No debts tracked yet
                            </p>
                            <p className="mb-4 text-sm text-muted-foreground">
                                Add your first debt to start tracking your payoff progress
                            </p>
                            <AddDebtDialog />
                        </CardContent>
                    </Card>
                )}

                {debts.length > 0 && (
                    <div className="grid gap-4">
                        {debts.map((debt) => (
                            <Card key={debt.id} className="hover:shadow-md transition-shadow">
                                <CardHeader>
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <div className="flex items-center gap-2 mb-1">
                                                <span
                                                    className={`inline-block h-2 w-2 rounded-full ${getDebtTypeColor(debt.type)}`}
                                                />
                                                <span className="text-xs font-medium text-muted-foreground uppercase">
                                                    {getDebtTypeLabel(debt.type)}
                                                </span>
                                            </div>
                                            <CardTitle className="text-xl">
                                                {debt.name}
                                            </CardTitle>
                                        </div>
                                        <Link href={show({ debt: debt.id }).url}>
                                            <Button variant="outline" size="sm">
                                                View Details
                                            </Button>
                                        </Link>
                                    </div>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className={`grid grid-cols-2 gap-4 ${debt.principal_amount ? 'lg:grid-cols-4' : 'lg:grid-cols-3'}`}>
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Current Balance
                                            </p>
                                            <p className="text-lg font-bold">
                                                {formatCurrency(debt.current_balance)}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Interest Rate
                                            </p>
                                            <p className="text-lg font-semibold">
                                                {debt.interest_rate}%
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Monthly Interest
                                            </p>
                                            <p className="text-lg font-semibold">
                                                {formatCurrency(debt.monthly_interest)}
                                            </p>
                                        </div>
                                        {debt.principal_amount && (
                                            <div>
                                                <p className="text-sm text-muted-foreground">
                                                    Original Loan
                                                </p>
                                                <p className="text-lg font-semibold">
                                                    {formatCurrency(debt.principal_amount)}
                                                </p>
                                            </div>
                                        )}
                                    </div>
                                    {debt.progress_percentage !== null && debt.principal_amount && (
                                        <div className="space-y-2">
                                            <div className="flex items-center justify-between text-sm">
                                                <span className="text-muted-foreground">
                                                    Payoff Progress
                                                </span>
                                                <span className="font-medium">
                                                    {debt.progress_percentage.toFixed(1)}%
                                                </span>
                                            </div>
                                            <Progress value={debt.progress_percentage} />
                                            <p className="text-xs text-muted-foreground">
                                                Paid {formatCurrency(debt.total_paid)} of{' '}
                                                {formatCurrency(debt.principal_amount)}
                                            </p>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
