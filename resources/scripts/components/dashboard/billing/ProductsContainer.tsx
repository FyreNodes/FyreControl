import React, { useEffect } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import Spinner from '@/components/elements/Spinner';
import useFlash from '@/plugins/useFlash';
import useSWR from 'swr';
import { RouteComponentProps } from 'react-router-dom';
import Button from '@/components/elements/Button';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import MessageBox from '@/components/elements/MessageBox';
import styled from 'styled-components/macro';
import { SwitchTransition } from 'react-transition-group';

import getProducts from '@/api/billing/getProducts';
import getShoppingCart from '@/api/billing/getShoppingCart';
import addToShoppingCart from '@/api/billing/addToShoppingCart';
import http from '@/api/http';
import getPlans from '@/api/store/getPlans';

type Props = {
    id: string;
}

export default ({ match }: RouteComponentProps<Props>) => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const id = match.params.id;
    const { data, error } = useSWR<any>([ '/billing/plans' ], () => getPlans());
    const { data: shoppingcart, error: shoppingerror, mutate: shoppingmutate } = useSWR<any>([ '/billing/shoppingcart' ], () => getShoppingCart(), {
        revalidateOnFocus: true,
    });

    useEffect(() => {
        if (!shoppingcart) {
            return;
        }
        if (!data) {
            return;
        }
        const element = document.getElementById('shoppingcart_message') as HTMLParagraphElement;
        if (shoppingcart.totalamount > 0) {
            if (shoppingcart.totalamount > 1) {
                element.innerHTML = `You have ${shoppingcart.totalamount} items in the shopping cart.`;
            } else {
                element.innerHTML = 'You have 1 item in the shopping cart.';
            }
        } else {
            element.innerHTML = 'You have 0 items in the shopping cart.';
        }

        if (!error) {
            clearFlashes('billing:products');
        } else {
            clearAndAddHttpError({ key: 'billing:products', error });
        }
    }, [ shoppingcart, data ]);

    const Toast = styled.div`
    ${tw`fixed z-50 bottom-0 left-0 mb-4 w-full flex justify-center lg:justify-end lg:pr-4`};
    & > div {
        ${tw`rounded px-4 py-2 border`};
        background-color: #1f2933;
        border-color: #000;
        opacity: ${75 / 100};
    }
    & p {
        color: #fff;
    }
`;

    return (
        <PageContentBlock title={'Shop'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'billing:products'} css={tw`mb-4`} />
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
                                There are no products in this category.
                            </MessageBox>
                        </div>
                        :
                        <div css={tw`w-full flex flex-wrap justify-evenly`}>
                            {data.map((item: any) => (
                                <TitledGreyBox title={item.name} hideTitle noPadding css={`margin: 5px; margin-top: 4rem !important; ${tw`relative w-full lg:w-3/12`}`} customBorder>
                                    <div css={'height: 405px;border-radius: 5px;transition: all .3s;'}>
                                        <div css={tw`flex justify-center`}>
                                            <img src={item.icon} css={'width: 80px;height: 80px;margin-top: -35px;'} alt={''}/>
                                        </div>
                                        <div css={'display: table; margin 0 auto; text-align center;'}>
                                            <div css={tw`mt-5 w-full`}>
                                                <a>{item.name.toUpperCase()}</a>
                                            </div>
                                            <div css={tw`mt-2 w-full mb-5`}>
                                                <a css={'color: #fff'}>${(Math.round(item.price * 100) / 100).toFixed(2)}/Month</a>
                                            </div>
                                            <hr css={'background-color:#1cb1df;color:#1cb1df;height:4px;width:100%; margin: 0 auto;'} />
                                            <div css={tw`mt-5 mb-7`}>
                                                <span dangerouslySetInnerHTML={{ __html: item.description }}></span>
                                            </div>
                                            <div css={'margin: 0 auto;'}>
                                                <Button size={'small'} isSecondary id={`button_p_${item.id}`} onClick={() => { http.post('/api/client/products/subscribe', { id: item.id }).then(res => { if (!res.data) return; else window.location.href = res.data; }); }}>Get Started</Button>
                                            </div>
                                        </div>
                                    </div>
                                </TitledGreyBox>
                            ))}
                        </div>
                    }
                    {!shoppingcart ?
                        <Spinner size={'small'} centered />
                        :
                        <SwitchTransition>
                            <Toast>
                                <div css={'text-align: center;'}>
                                    <p css={'line-height: 1.75rem; font-size: 1.06rem;'} id={'shoppingcart_message'}>
                                        {shoppingcart.totalamount > 0 ?
                                            (shoppingcart.totalamount > 1 ?
                                                `You have ${shoppingcart.totalamount} items in the shopping cart.`
                                                :
                                                'You have 1 item in the shopping cart.'
                                            )
                                            :
                                            'You have 0 items in the shopping cart.'
                                        }
                                    </p>
                                    <a href={'/store/cart'}>View Shopping Cart</a>
                                </div>
                            </Toast>
                        </SwitchTransition>
                    }
                </>
            }
        </PageContentBlock>
    );
};
