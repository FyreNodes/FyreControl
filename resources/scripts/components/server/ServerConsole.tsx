import React, { lazy, memo } from 'react';
import tw from 'twin.macro';
import ServerContentBlock from '@/components/elements/ServerContentBlock';
import ServerDetailsBlock from '@/components/server/elements/ServerDetailsBlock';
import ServerResourceBlock from '@/components/server/elements/ServerResourceBlock';
import isEqual from 'react-fast-compare';
import ErrorBoundary from '@/components/elements/ErrorBoundary';
import Spinner from '@/components/elements/Spinner';

export type PowerAction = 'start' | 'stop' | 'restart' | 'kill';

const ChunkedConsole = lazy(() => import('@/components/server/elements/Console'));
const StatGraphs = lazy(() => import('@/components/server/elements/StatGraphs'));

const ServerConsole = () => {
    return (
        <ServerContentBlock title={'Console'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full lg:w-1/4`}>
                <ServerDetailsBlock/>
                <ServerResourceBlock/>
            </div>
            <div css={tw`w-full lg:w-3/4 mt-4 lg:mt-0 lg:pl-4`}>
                <Spinner.Suspense>
                    <ErrorBoundary>
                        <ChunkedConsole/>
                    </ErrorBoundary>
                    <StatGraphs/>
                </Spinner.Suspense>
            </div>
        </ServerContentBlock>
    );
};

export default memo(ServerConsole, isEqual);
