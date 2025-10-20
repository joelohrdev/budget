import PayPeriodsController from '@/actions/App/Http/Controllers/PayPeriodsController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { DatePicker } from '@/components/ui/date-picker';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/pay-periods';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/react';
import { ArrowLeftIcon } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pay Periods',
        href: index().url,
    },
    {
        title: 'Create',
        href: '/pay-periods/create',
    },
];

export default function CreatePayPeriod() {
    const getNextFriday = () => {
        const today = new Date();
        const dayOfWeek = today.getDay();
        const daysUntilFriday = (5 - dayOfWeek + 7) % 7 || 7;
        const nextFriday = new Date(today);
        nextFriday.setDate(today.getDate() + daysUntilFriday);

        return nextFriday.toISOString().split('T')[0];
    };

    const getEndDate = (startDate: string) => {
        const start = new Date(startDate);
        const end = new Date(start);
        end.setDate(start.getDate() + 13);

        return end.toISOString().split('T')[0];
    };

    const defaultStartDate = getNextFriday();
    const defaultEndDate = getEndDate(defaultStartDate);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Pay Period" />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-4">
                    <Link href={index().url}>
                        <Button variant="outline" size="icon">
                            <ArrowLeftIcon className="size-4" />
                        </Button>
                    </Link>
                    <Heading
                        title="Create Pay Period"
                        description="Set up a new bi-weekly pay period and card budgets"
                    />
                </div>

                <Card className="mx-auto w-full max-w-2xl">
                    <CardHeader>
                        <CardTitle>New Pay Period</CardTitle>
                        <CardDescription>
                            This will deactivate any existing active pay period
                            and create a new one.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...PayPeriodsController.store.form()}
                            className="space-y-6"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <div className="grid gap-6 md:grid-cols-2">
                                        <div className="grid gap-2">
                                            <Label htmlFor="start_date">
                                                Start Date
                                            </Label>
                                            <DatePicker
                                                id="start_date"
                                                name="start_date"
                                                defaultValue={defaultStartDate}
                                                required
                                            />
                                            <InputError
                                                message={errors.start_date}
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="end_date">
                                                End Date
                                            </Label>
                                            <DatePicker
                                                id="end_date"
                                                name="end_date"
                                                defaultValue={defaultEndDate}
                                                required
                                            />
                                            <InputError
                                                message={errors.end_date}
                                            />
                                        </div>
                                    </div>

                                    <div className="space-y-4">
                                        <h3 className="text-lg font-semibold">
                                            Card Budgets
                                        </h3>

                                        <div className="grid gap-2">
                                            <Label htmlFor="debit_card_budget">
                                                Debit Card Budget
                                            </Label>
                                            <Input
                                                id="debit_card_budget"
                                                name="debit_card_budget"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="0.00"
                                                required
                                            />
                                            <InputError
                                                message={
                                                    errors.debit_card_budget
                                                }
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="credit_card_budget">
                                                Credit Card Budget
                                            </Label>
                                            <Input
                                                id="credit_card_budget"
                                                name="credit_card_budget"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="0.00"
                                                required
                                            />
                                            <InputError
                                                message={
                                                    errors.credit_card_budget
                                                }
                                            />
                                        </div>
                                    </div>

                                    <div className="flex flex-col gap-4 sm:flex-row">
                                        <Button
                                            type="submit"
                                            disabled={processing}
                                            className="w-full sm:w-auto"
                                        >
                                            Create Pay Period
                                        </Button>
                                        <Link href={index().url}>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                className="w-full sm:w-auto"
                                            >
                                                Cancel
                                            </Button>
                                        </Link>
                                    </div>
                                </>
                            )}
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
