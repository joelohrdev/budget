import Heading from '@/components/heading';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import {
    BarChart3Icon,
    TrendingDownIcon,
    TrendingUpIcon,
    DollarSignIcon,
    PieChartIcon,
    ArrowUpIcon,
    ArrowDownIcon,
} from 'lucide-react';

type CategorySpending = {
    id: number;
    name: string;
    color: string | null;
    total: number;
    transaction_count: number;
    average: number;
};

type BudgetVsActual = {
    name: string;
    type: string;
    budget_limit: number;
    spent: number;
    remaining: number;
    percent_used: number;
    over_budget: boolean;
};

type SpendingTrend = {
    month: string;
    total: number;
    categories: Record<string, number>;
};

type MonthOverMonth = {
    current_month: {
        month: string;
        total: number;
    };
    previous_month: {
        month: string;
        total: number;
    };
    difference: number;
    percent_change: number;
};

type Summary = {
    total_spent: number;
    total_credits: number;
    net_spending: number;
    transaction_count: number;
    average_transaction: number;
};

type Props = {
    summary: Summary;
    categorySpending: CategorySpending[];
    topCategories: CategorySpending[];
    budgetVsActual: BudgetVsActual[];
    spendingTrends: SpendingTrend[];
    monthOverMonth: MonthOverMonth;
    filters: {
        start_date: string | null;
        end_date: string | null;
    };
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Analytics',
        href: '/analytics',
    },
];

export default function Analytics({
    summary,
    categorySpending,
    topCategories,
    budgetVsActual,
    spendingTrends,
    monthOverMonth,
}: Props) {
    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(amount);
    };

    const formatMonth = (monthString: string) => {
        const [year, month] = monthString.split('-');
        return new Date(Number(year), Number(month) - 1).toLocaleDateString('en-US', {
            month: 'short',
            year: 'numeric',
        });
    };

    const getMaxSpending = () => {
        if (categorySpending.length === 0) {
            return 0;
        }

        return Math.max(...categorySpending.map((c) => c.total));
    };

    const maxSpending = getMaxSpending();

    const getMaxTrendValue = () => {
        if (spendingTrends.length === 0) {
            return 0;
        }

        return Math.max(...spendingTrends.map((t) => t.total));
    };

    const maxTrend = getMaxTrendValue();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Analytics" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <Heading
                    title="Analytics"
                    description="Insights into your spending patterns and budget performance"
                />

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Spent
                            </CardTitle>
                            <DollarSignIcon className="size-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {formatCurrency(summary.total_spent)}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                {summary.transaction_count} transactions
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Credits Received
                            </CardTitle>
                            <TrendingUpIcon className="size-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {formatCurrency(summary.total_credits)}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Returns & refunds
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Net Spending
                            </CardTitle>
                            <TrendingDownIcon className="size-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {formatCurrency(summary.net_spending)}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                After credits applied
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Average Transaction
                            </CardTitle>
                            <BarChart3Icon className="size-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {formatCurrency(summary.average_transaction)}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Per transaction
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {monthOverMonth.current_month && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <TrendingUpIcon className="size-5" />
                                Month-over-Month Comparison
                            </CardTitle>
                            <CardDescription>
                                Comparing {formatMonth(monthOverMonth.current_month.month)} to{' '}
                                {formatMonth(monthOverMonth.previous_month.month)}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="grid gap-4 sm:grid-cols-3">
                                <div className="rounded-lg border p-4">
                                    <div className="text-sm text-muted-foreground">
                                        Current Month
                                    </div>
                                    <div className="mt-2 text-2xl font-bold">
                                        {formatCurrency(monthOverMonth.current_month.total)}
                                    </div>
                                </div>

                                <div className="rounded-lg border p-4">
                                    <div className="text-sm text-muted-foreground">
                                        Previous Month
                                    </div>
                                    <div className="mt-2 text-2xl font-bold">
                                        {formatCurrency(monthOverMonth.previous_month.total)}
                                    </div>
                                </div>

                                <div className="rounded-lg border p-4">
                                    <div className="text-sm text-muted-foreground">
                                        Change
                                    </div>
                                    <div className="mt-2 flex items-baseline gap-2">
                                        <span className="text-2xl font-bold">
                                            {monthOverMonth.percent_change > 0 ? '+' : ''}
                                            {monthOverMonth.percent_change.toFixed(1)}%
                                        </span>
                                        {monthOverMonth.percent_change > 0 ? (
                                            <ArrowUpIcon className="size-4 text-red-500" />
                                        ) : monthOverMonth.percent_change < 0 ? (
                                            <ArrowDownIcon className="size-4 text-green-500" />
                                        ) : null}
                                    </div>
                                    <div className="mt-1 text-xs text-muted-foreground">
                                        {formatCurrency(Math.abs(monthOverMonth.difference))}
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {budgetVsActual.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <BarChart3Icon className="size-5" />
                                Budget vs Actual (Current Pay Period)
                            </CardTitle>
                            <CardDescription>
                                How you're tracking against your budget
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {budgetVsActual.map((item, index) => (
                                    <div key={index} className="space-y-2">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <div className="font-medium">{item.name}</div>
                                                <div className="text-sm text-muted-foreground">
                                                    {formatCurrency(item.spent)} of{' '}
                                                    {formatCurrency(item.budget_limit)}
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <div
                                                    className={`text-lg font-bold ${item.over_budget ? 'text-red-600 dark:text-red-400' : ''}`}
                                                >
                                                    {item.percent_used.toFixed(1)}%
                                                </div>
                                                <div className="text-sm text-muted-foreground">
                                                    {formatCurrency(item.remaining)} left
                                                </div>
                                            </div>
                                        </div>
                                        <div className="h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-800">
                                            <div
                                                className={`h-full transition-all ${
                                                    item.over_budget
                                                        ? 'bg-red-500'
                                                        : item.percent_used >= 90
                                                          ? 'bg-red-500'
                                                          : item.percent_used >= 75
                                                            ? 'bg-amber-500'
                                                            : 'bg-green-500'
                                                }`}
                                                style={{
                                                    width: `${Math.min(item.percent_used, 100)}%`,
                                                }}
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                )}

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <PieChartIcon className="size-5" />
                                Top Spending Categories
                            </CardTitle>
                            <CardDescription>
                                Your highest spending categories
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {topCategories.length > 0 ? (
                                <div className="space-y-4">
                                    {topCategories.map((category) => (
                                        <div key={category.id} className="space-y-2">
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center gap-2">
                                                    {category.color && (
                                                        <div
                                                            className="size-3 rounded-full"
                                                            style={{
                                                                backgroundColor: category.color,
                                                            }}
                                                        />
                                                    )}
                                                    <span className="font-medium">
                                                        {category.name}
                                                    </span>
                                                </div>
                                                <div className="text-right">
                                                    <div className="font-bold">
                                                        {formatCurrency(category.total)}
                                                    </div>
                                                    <div className="text-xs text-muted-foreground">
                                                        {category.transaction_count} transactions
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-800">
                                                <div
                                                    className="h-full transition-all"
                                                    style={{
                                                        width: `${(category.total / maxSpending) * 100}%`,
                                                        backgroundColor:
                                                            category.color || '#3b82f6',
                                                    }}
                                                />
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="py-8 text-center text-muted-foreground">
                                    No spending data available
                                </p>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <BarChart3Icon className="size-5" />
                                All Categories
                            </CardTitle>
                            <CardDescription>
                                Complete spending breakdown by category
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {categorySpending.length > 0 ? (
                                <div className="space-y-3">
                                    {categorySpending.map((category) => (
                                        <div
                                            key={category.id}
                                            className="flex items-center justify-between rounded-lg border p-3"
                                        >
                                            <div className="flex items-center gap-2">
                                                {category.color && (
                                                    <div
                                                        className="size-3 rounded-full"
                                                        style={{
                                                            backgroundColor: category.color,
                                                        }}
                                                    />
                                                )}
                                                <div>
                                                    <div className="font-medium">
                                                        {category.name}
                                                    </div>
                                                    <div className="text-xs text-muted-foreground">
                                                        Avg: {formatCurrency(category.average)}
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <div className="font-bold">
                                                    {formatCurrency(category.total)}
                                                </div>
                                                <div className="text-xs text-muted-foreground">
                                                    {category.transaction_count} txns
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="py-8 text-center text-muted-foreground">
                                    No spending data available
                                </p>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {spendingTrends.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <TrendingUpIcon className="size-5" />
                                Spending Trends
                            </CardTitle>
                            <CardDescription>
                                Your spending over the last 6 months
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {spendingTrends.map((trend) => (
                                    <div key={trend.month} className="space-y-2">
                                        <div className="flex items-center justify-between">
                                            <span className="font-medium">
                                                {formatMonth(trend.month)}
                                            </span>
                                            <span className="font-bold">
                                                {formatCurrency(trend.total)}
                                            </span>
                                        </div>
                                        <div className="h-8 overflow-hidden rounded-lg bg-neutral-200 dark:bg-neutral-800">
                                            <div
                                                className="h-full bg-blue-500 transition-all"
                                                style={{
                                                    width: `${(trend.total / maxTrend) * 100}%`,
                                                }}
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}
