import AddTransactionDialog from '@/components/add-transaction-dialog';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { create } from '@/routes/pay-periods';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import {
    CalendarIcon,
    CreditCardIcon,
    PlusIcon,
    TrendingDownIcon,
    TrendingUpIcon,
} from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

type CardData = {
    id: number;
    name: string;
    type: 'debit' | 'credit';
    budget_limit: number;
    total_spent: number;
    total_credits: number;
    remaining_budget: number;
};

type Category = {
    id: number;
    name: string;
    color: string | null;
};

type ActivePayPeriod = {
    start_date: string;
    end_date: string;
    cards: CardData[];
};

type Props = {
    activePayPeriod: ActivePayPeriod | null;
    categories: Category[];
};

export default function Dashboard({ activePayPeriod, categories }: Props) {
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

    const getProgressPercentage = (spent: number, limit: number) => {
        if (limit === 0) {
            return 0;
        }

        return Math.min((spent / limit) * 100, 100);
    };

    const getProgressColor = (percentage: number) => {
        if (percentage >= 90) {
            return 'bg-red-500';
        }

        if (percentage >= 75) {
            return 'bg-amber-500';
        }

        return 'bg-green-500';
    };

    const activeCards =
        activePayPeriod?.cards.map((card) => ({
            id: card.id,
            name: card.name,
            type: card.type,
        })) || [];

    const totalBudget = activePayPeriod?.cards.reduce(
        (sum, card) => sum + card.budget_limit,
        0,
    ) || 0;

    const totalRemaining = activePayPeriod?.cards.reduce(
        (sum, card) => sum + card.remaining_budget,
        0,
    ) || 0;

    const totalSpent = activePayPeriod?.cards.reduce(
        (sum, card) => sum + card.total_spent,
        0,
    ) || 0;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Dashboard"
                        description="Overview of your current pay period"
                    />
                    {activeCards.length > 0 && (
                        <AddTransactionDialog
                            cards={activeCards}
                            categories={categories}
                        />
                    )}
                </div>

                {!activePayPeriod && (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <CalendarIcon className="mb-4 size-12 text-muted-foreground" />
                            <p className="mb-2 text-lg font-medium">
                                No active pay period
                            </p>
                            <p className="mb-4 text-sm text-muted-foreground">
                                Create your first pay period to start tracking
                                your budget
                            </p>
                            <Link href={create().url}>
                                <Button>
                                    <PlusIcon className="size-4" />
                                    Create Pay Period
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>
                )}

                {activePayPeriod && (
                    <>
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <CalendarIcon className="size-5" />
                                    Current Pay Period
                                </CardTitle>
                                <CardDescription className="flex flex-col gap-1 sm:flex-row sm:gap-1">
                                    <span>{formatDate(activePayPeriod.start_date)}</span>
                                    <span className="hidden sm:inline">-</span>
                                    <span>{formatDate(activePayPeriod.end_date)}</span>
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div className="rounded-lg border p-4">
                                        <div className="flex items-center gap-2 text-muted-foreground">
                                            <TrendingUpIcon className="size-4" />
                                            <span className="text-sm">
                                                Total Budget
                                            </span>
                                        </div>
                                        <p className="mt-2 text-2xl font-bold">
                                            {formatCurrency(totalBudget)}
                                        </p>
                                    </div>

                                    <div className="rounded-lg border p-4">
                                        <div className="flex items-center gap-2 text-muted-foreground">
                                            <TrendingDownIcon className="size-4" />
                                            <span className="text-sm">
                                                Total Spent
                                            </span>
                                        </div>
                                        <p className="mt-2 text-2xl font-bold">
                                            {formatCurrency(totalSpent)}
                                        </p>
                                    </div>

                                    <div className="rounded-lg border p-4">
                                        <div className="flex items-center gap-2 text-muted-foreground">
                                            <CreditCardIcon className="size-4" />
                                            <span className="text-sm">
                                                Remaining
                                            </span>
                                        </div>
                                        <p className="mt-2 text-2xl font-bold">
                                            {formatCurrency(totalRemaining)}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <div className="grid gap-6 sm:grid-cols-1 lg:grid-cols-2">
                            {activePayPeriod.cards.map((card) => {
                                const percentage = getProgressPercentage(
                                    card.total_spent - card.total_credits,
                                    card.budget_limit,
                                );

                                return (
                                    <Card key={card.id}>
                                        <CardHeader>
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center gap-2">
                                                    <CreditCardIcon className="size-5 text-muted-foreground" />
                                                    <CardTitle>
                                                        {card.name}
                                                    </CardTitle>
                                                </div>
                                                <span className="text-xs capitalize text-muted-foreground">
                                                    {card.type}
                                                </span>
                                            </div>
                                        </CardHeader>
                                        <CardContent className="space-y-4">
                                            <div className="flex items-baseline justify-between">
                                                <span className="text-sm text-muted-foreground">
                                                    Remaining
                                                </span>
                                                <span className="text-3xl font-bold">
                                                    {formatCurrency(
                                                        card.remaining_budget,
                                                    )}
                                                </span>
                                            </div>

                                            <div className="h-3 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-800">
                                                <div
                                                    className={`h-full transition-all ${getProgressColor(percentage)}`}
                                                    style={{
                                                        width: `${percentage}%`,
                                                    }}
                                                />
                                            </div>

                                            <div className="flex justify-between text-sm text-muted-foreground">
                                                <span>
                                                    Spent:{' '}
                                                    {formatCurrency(
                                                        card.total_spent,
                                                    )}
                                                </span>
                                                <span>
                                                    Limit:{' '}
                                                    {formatCurrency(
                                                        card.budget_limit,
                                                    )}
                                                </span>
                                            </div>

                                            {card.total_credits > 0 && (
                                                <div className="text-sm text-green-600 dark:text-green-400">
                                                    Credits:{' '}
                                                    {formatCurrency(
                                                        card.total_credits,
                                                    )}
                                                </div>
                                            )}
                                        </CardContent>
                                    </Card>
                                );
                            })}
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
