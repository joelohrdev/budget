import AddTransactionDialog from '@/components/add-transaction-dialog';
import EditTransactionDialog from '@/components/edit-transaction-dialog';
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
import { create, edit } from '@/routes/pay-periods';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import {
    CalendarIcon,
    CreditCardIcon,
    PencilIcon,
    PlusIcon,
    TrashIcon,
} from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pay Periods',
        href: '/pay-periods',
    },
];

type Category = {
    id: number;
    name: string;
    color: string | null;
};

type TransactionData = {
    id: number;
    description: string;
    amount: number;
    type: 'debit' | 'credit';
    transaction_date: string;
    category: Category | null;
};

type CardData = {
    id: number;
    name: string;
    type: 'debit' | 'credit';
    budget_limit: number;
    total_spent: number;
    total_credits: number;
    remaining_budget: number;
    transactions: TransactionData[];
};

type PayPeriodData = {
    id: number;
    start_date: string;
    end_date: string;
    is_active: boolean;
    cards: CardData[];
};

type Props = {
    payPeriods: PayPeriodData[];
    categories: Category[];
};

export default function PayPeriodsIndex({ payPeriods, categories }: Props) {
    const [selectedCategory, setSelectedCategory] = useState<number | null>(
        null,
    );

    const activePayPeriod = payPeriods.find((pp) => pp.is_active);
    const activeCards =
        activePayPeriod?.cards.map((card) => ({
            id: card.id,
            name: card.name,
            type: card.type,
        })) || [];

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

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pay Periods" />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Pay Periods"
                        description="Manage your bi-weekly pay periods and budgets"
                    />
                    <div className="flex flex-wrap gap-2">
                        {activeCards.length > 0 && (
                            <AddTransactionDialog
                                cards={activeCards}
                                categories={categories}
                            />
                        )}
                        <Link href={create().url}>
                            <Button className="w-full sm:w-auto">
                                <PlusIcon className="size-4" />
                                New Pay Period
                            </Button>
                        </Link>
                    </div>
                </div>

                {categories.length > 0 && (
                    <div className="flex flex-wrap items-center gap-2">
                        <span className="text-sm font-medium">
                            Filter by category:
                        </span>
                        <Button
                            variant={
                                selectedCategory === null
                                    ? 'default'
                                    : 'outline'
                            }
                            size="sm"
                            onClick={() => setSelectedCategory(null)}
                        >
                            All
                        </Button>
                        {categories.map((category) => (
                            <Button
                                key={category.id}
                                variant={
                                    selectedCategory === category.id
                                        ? 'default'
                                        : 'outline'
                                }
                                size="sm"
                                onClick={() => setSelectedCategory(category.id)}
                                style={
                                    selectedCategory === category.id &&
                                    category.color
                                        ? {
                                              backgroundColor: category.color,
                                              borderColor: category.color,
                                          }
                                        : {}
                                }
                            >
                                {category.name}
                            </Button>
                        ))}
                    </div>
                )}

                {payPeriods.length === 0 && (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <CalendarIcon className="mb-4 size-12 text-muted-foreground" />
                            <p className="mb-2 text-lg font-medium">
                                No pay periods yet
                            </p>
                            <p className="mb-4 text-sm text-muted-foreground">
                                Get started by creating your first pay period
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

                <div className="grid gap-6">
                    {payPeriods.map((payPeriod) => (
                        <Card key={payPeriod.id}>
                            <CardHeader>
                                <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <CardTitle className="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-2">
                                            <div className="flex items-center gap-2">
                                                <CalendarIcon className="size-5" />
                                                <span className="text-sm sm:text-base">
                                                    {formatDate(payPeriod.start_date)}
                                                </span>
                                            </div>
                                            <span className="hidden sm:inline">-</span>
                                            <span className="text-sm sm:text-base">
                                                {formatDate(payPeriod.end_date)}
                                            </span>
                                        </CardTitle>
                                        <CardDescription className="mt-1">
                                            {payPeriod.is_active && (
                                                <span className="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Active
                                                </span>
                                            )}
                                        </CardDescription>
                                    </div>
                                    <Link
                                        href={edit({ payPeriod: payPeriod.id }).url}
                                    >
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            className="w-full sm:w-auto"
                                        >
                                            <PencilIcon className="size-4" />
                                            Edit
                                        </Button>
                                    </Link>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 sm:grid-cols-1 lg:grid-cols-2">
                                    {payPeriod.cards.map((card) => {
                                        const percentage = getProgressPercentage(
                                            card.total_spent -
                                                card.total_credits,
                                            card.budget_limit,
                                        );

                                        return (
                                            <div
                                                key={card.id}
                                                className="space-y-4 rounded-lg border p-4"
                                            >
                                                <div className="mb-3 flex items-center justify-between">
                                                    <div className="flex items-center gap-2">
                                                        <CreditCardIcon className="size-5 text-muted-foreground" />
                                                        <h3 className="font-semibold">
                                                            {card.name}
                                                        </h3>
                                                    </div>
                                                    <span className="text-xs capitalize text-muted-foreground">
                                                        {card.type}
                                                    </span>
                                                </div>

                                                <div className="space-y-2">
                                                    <div className="flex items-baseline justify-between">
                                                        <span className="text-sm text-muted-foreground">
                                                            Remaining
                                                        </span>
                                                        <span className="text-lg font-bold">
                                                            {formatCurrency(
                                                                card.remaining_budget,
                                                            )}
                                                        </span>
                                                    </div>

                                                    <div className="h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-800">
                                                        <div
                                                            className={`h-full transition-all ${getProgressColor(percentage)}`}
                                                            style={{
                                                                width: `${percentage}%`,
                                                            }}
                                                        />
                                                    </div>

                                                    <div className="flex justify-between text-xs text-muted-foreground">
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
                                                        <div className="text-xs text-green-600 dark:text-green-400">
                                                            Credits:{' '}
                                                            {formatCurrency(
                                                                card.total_credits,
                                                            )}
                                                        </div>
                                                    )}
                                                </div>

                                                {card.transactions.length > 0 && (
                                                    <div className="space-y-2">
                                                        <h4 className="text-xs font-medium text-muted-foreground">
                                                            Transactions
                                                        </h4>
                                                        <div className="space-y-1">
                                                            {card.transactions
                                                                .filter(
                                                                    (
                                                                        transaction,
                                                                    ) => {
                                                                        if (
                                                                            selectedCategory ===
                                                                            null
                                                                        ) {
                                                                            return true;
                                                                        }

                                                                        return (
                                                                            transaction
                                                                                .category
                                                                                ?.id ===
                                                                            selectedCategory
                                                                        );
                                                                    },
                                                                )
                                                                .map(
                                                                    (
                                                                        transaction,
                                                                    ) => (
                                                                    <div
                                                                        key={
                                                                            transaction.id
                                                                        }
                                                                        className="flex flex-col gap-2 rounded border p-2 text-xs sm:flex-row sm:items-start sm:justify-between"
                                                                    >
                                                                        <div className="min-w-0 flex-1 space-y-0.5">
                                                                            <div className="flex flex-wrap items-center gap-1.5">
                                                                                <div className="break-words font-medium">
                                                                                    {
                                                                                        transaction.description
                                                                                    }
                                                                                </div>
                                                                                {transaction.category && (
                                                                                    <span
                                                                                        className="inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                                                                        style={{
                                                                                            backgroundColor:
                                                                                                transaction
                                                                                                    .category
                                                                                                    .color ||
                                                                                                '#6b7280',
                                                                                            color: 'white',
                                                                                        }}
                                                                                    >
                                                                                        {
                                                                                            transaction
                                                                                                .category
                                                                                                .name
                                                                                        }
                                                                                    </span>
                                                                                )}
                                                                            </div>
                                                                            <div className="text-muted-foreground">
                                                                                {formatDate(
                                                                                    transaction.transaction_date,
                                                                                )}
                                                                            </div>
                                                                        </div>
                                                                        <div className="flex shrink-0 items-center justify-between gap-2 sm:justify-start">
                                                                            <span
                                                                                className={
                                                                                    transaction.type ===
                                                                                    'credit'
                                                                                        ? 'whitespace-nowrap font-semibold text-green-600 dark:text-green-400'
                                                                                        : 'whitespace-nowrap font-semibold'
                                                                                }
                                                                            >
                                                                                {transaction.type ===
                                                                                'credit'
                                                                                    ? '+'
                                                                                    : '-'}
                                                                                {formatCurrency(
                                                                                    transaction.amount,
                                                                                )}
                                                                            </span>
                                                                            <div className="flex items-center gap-2">
                                                                                <EditTransactionDialog
                                                                                    transaction={
                                                                                        transaction
                                                                                    }
                                                                                    cardId={
                                                                                        card.id
                                                                                    }
                                                                                    cards={
                                                                                        payPeriod.cards.map(
                                                                                            (
                                                                                                c,
                                                                                            ) => ({
                                                                                                id: c.id,
                                                                                                name: c.name,
                                                                                                type: c.type,
                                                                                            }),
                                                                                        )
                                                                                    }
                                                                                    categories={
                                                                                        categories
                                                                                    }
                                                                                />
                                                                                <button
                                                                                    onClick={() => {
                                                                                        if (
                                                                                            confirm(
                                                                                                'Are you sure you want to delete this transaction?',
                                                                                            )
                                                                                        ) {
                                                                                            router.delete(
                                                                                                `/transactions/${transaction.id}`,
                                                                                            );
                                                                                        }
                                                                                    }}
                                                                                    className="text-muted-foreground transition-colors hover:text-red-600"
                                                                                    aria-label="Delete transaction"
                                                                                >
                                                                                    <TrashIcon className="size-3.5" />
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                ),
                                                            )}
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        );
                                    })}
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
