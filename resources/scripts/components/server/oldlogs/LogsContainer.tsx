import React, { useEffect } from 'react';
import { ServerContext } from '@/state/server';
import useFlash from '@/plugins/useFlash';
import tw from 'twin.macro';
import useSWR from 'swr';
import Spinner from '@/components/elements/Spinner';
import GreyRowBox from '@/components/elements/GreyRowBox';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faFingerprint } from '@fortawesome/free-solid-svg-icons';
import FlashMessageRender from '@/components/FlashMessageRender';
import MessageBox from '@/components/elements/MessageBox';
import getLogs from '@/api/server/logs/getLogs';
import DeleteButton from '@/components/server/oldlogs/DeleteButton';
import ServerContentBlock from '@/components/elements/ServerContentBlock';

export interface LogsResponse {
    logs: any[];
}

export default () => {
    const uuid = ServerContext.useStoreState(state => state.server.data!.uuid);
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<LogsResponse>([ uuid, '/logs' ], (uuid) => getLogs(uuid), {
        revalidateOnFocus: false,
    });

    useEffect(() => {
        if (!error) {
            clearFlashes('logs');
        } else {
            clearAndAddHttpError({ key: 'logs', error });
        }
    });

    return (
        <ServerContentBlock title={'Logs'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'logs'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    <div css={tw`w-full`}>
                        {data.logs.length < 1 ?
                            <MessageBox type="info" title="Info">
                                There are no logs.
                            </MessageBox>
                            :
                            (data.logs.map((item, key) => (
                                <GreyRowBox css={tw`mb-2`} key={key}>
                                    <div css={tw`hidden md:block`}>
                                        <FontAwesomeIcon icon={faFingerprint} fixedWidth/>
                                    </div>
                                    <div css={tw`flex-initial ml-16 text-center`}>
                                        <p css={tw`text-sm`}>{item.module}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Module</p>
                                    </div>
                                    <div css={tw`flex-initial ml-16 text-center`}>
                                        <p css={tw`text-sm`}>{item.action}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Action</p>
                                    </div>
                                    <div css={tw`flex-initial ml-16 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.user}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>User</p>
                                    </div>
                                    <div css={tw`flex-1 ml-16 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.description}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Info</p>
                                    </div>
                                    <div css={tw`flex-initial ml-16 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.created_at}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Time</p>
                                    </div>
                                    <div css={tw`flex-initial ml-16 text-center`}>
                                        <DeleteButton logId={item.id} onDeleted={() => mutate()}/>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Delete</p>
                                    </div>
                                </GreyRowBox>
                            )))
                        }
                    </div>
                </>
            }
        </ServerContentBlock>
    );
};
