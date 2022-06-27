import React, { useEffect } from 'react';
import getPlans, {Plan} from '@/api/store/getPlans';
import useSWR from 'swr';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import Spinner from '@/components/elements/Spinner';
import Button from '@/components/elements/Button';
import { NavLink } from 'react-router-dom';
import ContentBox from '@/components/elements/ContentBox';
import FlashMessageRender from '@/components/FlashMessageRender';
import useFlash from '@/plugins/useFlash';
import PageLabel from "@/components/elements/PageLabel";
import {faStore} from "@fortawesome/free-solid-svg-icons";

export default () => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error } = useSWR('/billing/plans', () => getPlans());

    useEffect(() => {
        if (!error) {
            clearFlashes('store:plans');
        } else {
            clearAndAddHttpError({ key: 'store:plans', error });
        }
    });

    return (
        <PageContentBlock title={'Store • FyreNodes'}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'store:plans'} css={tw`mb-4`}/>
            </div>
            <PageLabel icon={faStore} title={'Store'} description={'Official store for deploying instances.'}/>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered/>
                </div>
                :
                <div css={tw`w-full flex flex-wrap justify-evenly`}>
                    {data.map((plan: Plan) => (
                        <ContentBox css={tw`relative w-8/12 sm:w-1/3 md:w-1/4 mt-16`}>
                            <div css={tw`flex justify-center`}>
                                <img src={plan.image} css={'width: 120px; height: 120px; margin-top: -70px;'} alt={'Plan Image'}/>
                            </div>
                            <div css={`${tw`my-0 mx-auto text-center transition-all rounded-md`} height: 405px;`}>
                                <div css={tw`mt-5 w-full`}>
                                    <a>{plan.name}</a>
                                </div>
                                <div css={tw`mt-2 w-full mb-5`}>
                                    {plan.price === '0' ?
                                        <a css={tw`text-white`}>Free</a>
                                        :
                                        <a css={tw`text-white`}>${plan.price} /month</a>
                                    }
                                </div>
                                <hr css={`${tw`w-5/7 h-1 my-0 mx-auto`} background-color: var(--theme-color); color: var(--theme-color); border-color: var(--theme-color);`}/>
                                <div css={tw`mt-5 mb-7`}>
                                    <span dangerouslySetInnerHTML={{ __html: plan.description }}/>
                                </div>
                                <div css={`${tw`bottom-12 absolute left-1/2`} margin-left: -64px;`}>
                                    <NavLink to={`/store/configure/${plan.id}`}>
                                        <Button size={'small'} isSecondary>Get Started</Button>
                                    </NavLink>
                                </div>
                            </div>
                        </ContentBox>
                    ))}
                </div>
            }
        </PageContentBlock>
    );
};
