import { Head, Link } from '@inertiajs/react';
import { login } from '@/routes';

export default function Welcome() {
    return (
        <>
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link
                    href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
                    rel="stylesheet"
                />
            </Head>

            <div className="flex min-h-screen w-full items-center justify-center overflow-x-hidden bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-gray-900 dark:to-gray-800">
                <div className="w-full max-w-md px-4 text-center">
                    <Link
                        href={login.url()}
                        className="inline-block transition-transform hover:scale-105"
                    >
                        <img
                            src="/favicon.svg"
                            alt="Budget App"
                            className="mx-auto h-32 w-32 drop-shadow-lg"
                        />
                    </Link>
                    <h1 className="mt-8 text-4xl font-semibold text-gray-900 dark:text-white">
                        Budget
                    </h1>
                    <p className="mt-2 text-lg text-gray-600 dark:text-gray-300">
                        Track your finances with ease
                    </p>
                    <Link
                        href={login.url()}
                        className="mt-8 inline-flex items-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-3 text-base font-medium text-white shadow-lg transition-all hover:from-emerald-600 hover:to-teal-700 hover:shadow-xl"
                    >
                        Get Started
                    </Link>
                </div>
            </div>
        </>
    );
}
