import React, { useEffect } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import Spinner from '@/components/elements/Spinner';
import useFlash from '@/plugins/useFlash';
import useSWR from 'swr';
import GreyRowBox from '@/components/elements/GreyRowBox';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faPlus, faMinus, faTrash } from '@fortawesome/free-solid-svg-icons';
import getShoppingCart from '@/api/billing/getShoppingCart';
import addToShoppingCart from '@/api/billing/addToShoppingCart';
import Button from '@/components/elements/Button';
import TitledGreyBox from '@/components/elements/TitledGreyBox';

export default () => {
    const { clearFlashes, addFlash } = useFlash();
    const { data: shoppingcart, error: shoppingerror, mutate: shoppingmutate } = useSWR<any>([ '/billing/shoppingcart' ], () => getShoppingCart(), {
        revalidateOnFocus: true,
    });

    async function editShoppingcart (amount: any, id: any, current: any) {
        if (amount > 0) {
            await addToShoppingCart(id);
        } else {
            if (current > 1) {
                await addToShoppingCart(id, -1);
            } else {
                await addToShoppingCart(id, -1);
            }
        }
        await shoppingmutate();
    }

    useEffect(() => {
        if (!shoppingcart) {
            return;
        }

        if (!shoppingerror) {
            clearFlashes('billing:cart');
        } else {
            addFlash({ key: 'billing:cart', message: shoppingerror.toString(), type: 'error' });
        }
    }, [ shoppingcart ]);

    return (
        <PageContentBlock title={'Shop'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'billing:cart'} css={tw`mb-4`} />
            </div>
            <div css={tw`w-full`}>
                <GreyRowBox css={`pointer-events: none; ${tw`mb-4 w-full flex justify-between`}`}>
                    <div>View shoppingcart</div>
                    {!shoppingcart ?
                        <div>Loading items</div>
                        :
                        (shoppingcart.totalamount > 1) ?
                            <div>{shoppingcart.totalamount} items</div>
                            : shoppingcart.totalamount == 1 ?
                                <div>{shoppingcart.totalamount} item</div>
                                :
                                <div>{shoppingcart.totalamount} items</div>
                    }
                </GreyRowBox>
            </div>
            <div css={tw`flex justify-between w-full flex-wrap`}>
                <div css={`${tw`flex flex-wrap w-full lg:w-7/12 items-start`}`}>
                    {!shoppingcart ?
                        <div css={tw`w-full`}>
                            <Spinner size={'large'} centered />
                        </div>
                        :
                        (shoppingcart.totalamount > 0 ?
                            (shoppingcart.cart.map((cartitem: any) => (
                                <GreyRowBox css={`:hover {cursor: pointer;}; ${tw`mb-2 w-full flex`}`}>
                                    <div css={`width: 100px; ${tw`flex justify-between mr-6`}`}>
                                        <div css={tw`self-center`}>
                                            <FontAwesomeIcon icon={faPlus} css={':hover {cursor: pointer;};'} onClick={() => { editShoppingcart(1, cartitem.product_id, cartitem.amount); }} fixedWidth />
                                        </div>
                                        <div css={`user-select: none; ${tw`self-center text-lg`}`}>
                                            <p>{cartitem.amount}</p>
                                        </div>
                                        <div css={tw`self-center`}>
                                            {cartitem.amount > 1 ?
                                                <FontAwesomeIcon icon={faMinus} css={':hover {cursor: pointer;};'} onClick={() => { editShoppingcart(-1, cartitem.product_id, cartitem.amount); }} fixedWidth />
                                                :
                                                <FontAwesomeIcon icon={faTrash} css={':hover {cursor: pointer;};'} onClick={() => { editShoppingcart(-1, cartitem.product_id, cartitem.amount); }} fixedWidth />
                                            }
                                        </div>
                                    </div>
                                    <div css={tw`flex-1 ml-4`}>
                                        <p css={'user-select: none;'}>{cartitem.name}</p>
                                        <p css={`user-select: none; ${tw`text-xs text-neutral-400`}`}>
                                            {cartitem.category}
                                        </p>
                                    </div>
                                    <div css={'user-select: none;'}>
                                        ${(Math.round(cartitem.price * 100) / 100).toFixed(2)}
                                    </div>
                                </GreyRowBox>
                            )))
                            :
                            <div>The shoppingcart is empty, click <a href="/store" css={'color: #0550B3;'}>here</a> to order a product</div>
                        )
                    }
                </div>
                <div css={`${tw`flex flex-wrap w-full lg:w-4/12 justify-end`}`}>
                    <TitledGreyBox title={''} hideTitle css={`display: block; user-select: none; ${tw`w-full`}`}>
                        <div css={'text-align: center;'}>
                            <a css={'font-size: 1.5rem'}>Summary</a>
                        </div>
                        <div css={tw`flex justify-between`}>
                            <div>
                                Subtotal:
                            </div>
                            <div>
                                {!shoppingcart ?
                                    'Loading price'
                                    :
                                    `$${(Math.round(shoppingcart.totalprice * 100) / 100).toFixed(2)}`
                                }
                            </div>
                        </div>
                        <div css={tw`flex justify-between`}>
                            <div>
                                VAT (0%):
                            </div>
                            <div>
                                $0.00
                            </div>
                        </div>
                        <div css={tw`flex justify-between`}>
                            <div>
                                Total:
                            </div>
                            <div>
                                {!shoppingcart ?
                                    'Loading price'
                                    :
                                    `$${(Math.round(shoppingcart.totalprice * 100) / 100).toFixed(2)}`
                                }
                            </div>
                        </div>
                        <div css={`margin-top: 1rem; ${tw`flex justify-center`}`}>
                            {!shoppingcart ?
                                <Button css={'width: 100%; :hover {cursor: default;};'} disabled color={'primary'}>Loading...</Button>
                                :
                                (shoppingcart.totalamount > 0 ?
                                    <Button css={'width: 100%; :hover {cursor: pointer;};'} color={'primary'}>Order</Button>
                                    :
                                    <Button css={'width: 100%; :hover {cursor: default;};'} disabled color={'primary'}>Order</Button>
                                )
                            }
                        </div>
                    </TitledGreyBox>
                </div>
            </div>
        </PageContentBlock>
    );
};
