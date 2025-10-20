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

type PayPeriod = {
    id: number;
    start_date: string;
    end_date: string;
    is_active: boolean;
    debit_card_budget: number;
    credit_card_budget: number;
    debit_card_id?: number;
    credit_card_id?: number;
};

type Props = {
    payPeriod: PayPeriod;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pay Periods',
        href: index().url,
    },
    {
        title: 'Edit',
        href: '#',
    },
];

export default function EditPayPeriod({ payPeriod }: Props) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Pay Period" />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-4">
                    <Link href={index().url}>
                        <Button variant="outline" size="icon">
                            <ArrowLeftIcon className="size-4" />
                        </Button>
                    </Link>
                    <Heading
                        title="Edit Pay Period"
                        description="Update pay period dates and card budgets"
                    />
                </div>

                <Card className="mx-auto w-full max-w-2xl">
                    <CardHeader>
                        <CardTitle>Edit Pay Period</CardTitle>
                        <CardDescription>
                            Modify the dates and budget limits for this pay
                            period.
                            {payPeriod.is_active && (
                                <span className="ml-2 inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Active
                                </span>
                            )}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...PayPeriodsController.update.form({
                                payPeriod: payPeriod.id,
                            })}
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
                                                defaultValue={
                                                    payPeriod.start_date
                                                }
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
                                                defaultValue={payPeriod.end_date}
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
                                                defaultValue={
                                                    payPeriod.debit_card_budget
                                                }
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
                                                defaultValue={
                                                    payPeriod.credit_card_budget
                                                }
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
                                            Update Pay Period
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
