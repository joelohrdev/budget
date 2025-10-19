import * as React from 'react';
import { format } from 'date-fns';
import { CalendarIcon } from 'lucide-react';

import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';

interface DatePickerProps {
    id?: string;
    name?: string;
    value?: string;
    defaultValue?: string;
    onChange?: (date: string) => void;
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
    className?: string;
}

export function DatePicker({
    id,
    name,
    value,
    defaultValue,
    onChange,
    placeholder = 'Pick a date',
    required = false,
    disabled = false,
    className,
}: DatePickerProps) {
    const parseDate = (dateString: string): Date => {
        const [year, month, day] = dateString.split('-').map(Number);
        return new Date(year, month - 1, day);
    };

    const [date, setDate] = React.useState<Date | undefined>(() => {
        const initialValue = value || defaultValue;
        return initialValue ? parseDate(initialValue) : undefined;
    });

    const formatDate = (date: Date): string => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    const handleSelect = (selectedDate: Date | undefined) => {
        setDate(selectedDate);
        if (onChange && selectedDate) {
            onChange(formatDate(selectedDate));
        }
    };

    return (
        <>
            <Popover>
                <PopoverTrigger asChild>
                    <Button
                        id={id}
                        variant="outline"
                        className={cn(
                            'w-full justify-start text-left font-normal',
                            !date && 'text-muted-foreground',
                            className,
                        )}
                        disabled={disabled}
                        type="button"
                    >
                        <CalendarIcon className="mr-2 size-4" />
                        {date ? (
                            format(date, 'PPP')
                        ) : (
                            <span>{placeholder}</span>
                        )}
                    </Button>
                </PopoverTrigger>
                <PopoverContent className="w-auto p-0" align="start">
                    <Calendar
                        mode="single"
                        selected={date}
                        onSelect={handleSelect}
                        initialFocus
                    />
                </PopoverContent>
            </Popover>
            {name && (
                <input
                    type="hidden"
                    name={name}
                    value={date ? formatDate(date) : ''}
                    required={required}
                />
            )}
        </>
    );
}
