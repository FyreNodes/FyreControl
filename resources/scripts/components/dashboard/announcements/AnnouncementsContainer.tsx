import React, { useEffect } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import Spinner from '@/components/elements/Spinner';
import useFlash from '@/plugins/useFlash';
import useSWR from 'swr';
import { Link } from 'react-router-dom';
import Button from '@/components/elements/Button';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import MessageBox from '@/components/elements/MessageBox';
import getAnnouncements from '@/api/announcements/getAnnouncements';
import PageLabel from "@/components/elements/PageLabel";
import { faBullhorn } from "@fortawesome/free-solid-svg-icons";

export interface AnnouncementsResponse {
    announcements: any[];
}

export default () => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error } = useSWR<AnnouncementsResponse>([ '/announcements' ], () => getAnnouncements());

    useEffect(() => {
        if (!error) {
            clearFlashes('announcements');
        } else {
            clearAndAddHttpError({ key: 'announcements', error });
        }
    });

    return (
        <PageContentBlock title={'Announcements • FyreNodes'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'announcements'} css={tw`mb-4`} />
            </div>
            <PageLabel icon={faBullhorn} title={'Announcements'} description={'Official service announcements.'}/>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered/>
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
                            <div css={tw`w-1/2 lg:pl-4 pt-4`} key={key}>
                                <TitledGreyBox title={item.title}>
                                    <div css={tw`px-1 py-2`}>
                                        <span dangerouslySetInnerHTML={{ __html: item.body.substr(0, 300) + (item.body.length > 300 ? '...' : '') }}/>
                                        <div css={tw`w-full pt-4`}>
                                            <span css={'float: right;'}>{item.updated_at}</span>
                                            <Link to={`/announcements/${item.id}`}>
                                                <Button size={'xsmall'} color={'primary'}>Read More</Button>
                                            </Link>
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
