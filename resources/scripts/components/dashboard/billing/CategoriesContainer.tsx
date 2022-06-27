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

import getCategories from '@/api/billing/getCategories';

export default () => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error } = useSWR<any>([ '/billing/categories' ], () => getCategories());

    useEffect(() => {
        if (!error) {
            clearFlashes('billing:index');
        } else {
            clearAndAddHttpError({ key: 'billing:index', error });
        }
    });

    return (
        <PageContentBlock title={'Shop'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'billing:index'} css={tw`mb-4`}/>
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    {data.length < 1 ?
                        <div css={tw`w-full`}>
                            <MessageBox type="info" title="Info">
                                There are no categories.
                            </MessageBox>
                        </div>
                        :
                        (data.map((item: any, key: any) => (
                            <div css={tw`w-full lg:w-4/12 lg:pl-4`} key={key}>
                                <TitledGreyBox title={item.name} hideTitle noPadding>
                                    <div css={tw`w-full`}>
                                        <img css={'width: 100%; height: 200px;'} src={item.icon} alt={''}/>
                                        <div css={tw`p-3`}>
                                            <p css={'font-size: 20px; font-weight: bold;'}>{item.name}</p>
                                            <span dangerouslySetInnerHTML={{ __html: item.description }}/>
                                            <Link to={`/store/products/${item.id}`}>
                                                <Button css={'width: 100%; margin-top: 1rem'} color={'primary'}>Shop</Button>
                                            </Link>
                                        </div>
                                    </div>
                                </TitledGreyBox>
                                <br/>
                            </div>
                        )))
                    }
                </>
            }
        </PageContentBlock>
    );
};
