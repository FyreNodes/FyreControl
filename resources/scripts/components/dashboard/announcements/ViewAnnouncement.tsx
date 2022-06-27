import React, { useEffect } from 'react';
import {RouteComponentProps, useRouteMatch} from 'react-router-dom';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import Spinner from '@/components/elements/Spinner';
import useFlash from '@/plugins/useFlash';
import useSWR from 'swr';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import MessageBox from '@/components/elements/MessageBox';

import viewAnnouncement from '@/api/announcements/viewAnnouncement';

export interface ViewAnnouncementResponse {
    announcements: any[];
}

type Props = {
    id: string;
}

export default () => {
    const id = useRouteMatch<{ id: string }>().params.id;

    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error } = useSWR<ViewAnnouncementResponse>([ id, '/announcements' ], ($id) => viewAnnouncement($id));

    useEffect(() => {
        if (!error) {
            clearFlashes('announcements');
        } else {
            clearAndAddHttpError({ key: 'announcements', error });
        }
    });

    return (
        <PageContentBlock title={'Viewing Announcement • FyreNodes'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'announcements'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    {data.announcements.length < 1 ?
                        <div css={tw`w-full`}>
                            <MessageBox type="info" title="Info">
                                There are no announcements.
                            </MessageBox>
                        </div>
                        :
                        (data.announcements.map((item, key) => (
                            <div css={tw`w-full lg:pl-4 pt-4`} key={key}>
                                <TitledGreyBox title={item.title}>
                                    <div css={tw`px-1 py-2`}>
                                        <span dangerouslySetInnerHTML={{ __html: item.body }}/>
                                        <br/>
                                        <div style={{ textAlign: 'right' }}>
                                            {item.updated_at}
                                        </div>
                                    </div>
                                </TitledGreyBox>
                            </div>
                        )))
                    }
                </>
            }
        </PageContentBlock>
    );
};
