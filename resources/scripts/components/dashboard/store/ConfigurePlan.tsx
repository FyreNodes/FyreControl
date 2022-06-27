import React, { useEffect, useState } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import {Form, Formik, FormikHelpers} from 'formik';
import {object, string} from 'yup';
import { useStoreState } from '@/state/hooks';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import { faLayerGroup } from '@fortawesome/free-solid-svg-icons';
import Field from '@/components/elements/Field';
import InputSpinner from '@/components/elements/InputSpinner';
import Button from '@/components/elements/Button';
import subscribe from '@/api/store/subscribe';
import FlashMessageRender from '@/components/FlashMessageRender';
import useFlash from '@/plugins/useFlash';
import { AxiosError } from 'axios';
import useSWR from 'swr';
import getTypes, {Type} from '@/api/store/getTypes';
import Select from '@/components/elements/Select';
import Spinner from '@/components/elements/Spinner';
import { httpErrorToHuman } from '@/api/http';
import {useRouteMatch} from 'react-router-dom';

interface ServerDetails {
    name: string;
    description: string;
}

export default () => {
    const match = useRouteMatch<{ id: string }>();
    const { clearFlashes, addFlash, clearAndAddHttpError } = useFlash();
    const { data, error } = useSWR('/billing/types', () => getTypes());
    const [ loading, setLoading ] = useState(false);
    const [ egg, setEgg ] = useState(0);
    const user = useStoreState(state => state.user.data!);

    useEffect(() => {
        if (data) setEgg(data[0].egg);
        if (!error) clearFlashes('store:conf'); else clearAndAddHttpError({ key: 'store:conf', error: error });
    }, [ data ]);

    const submit = ({ name, description }: ServerDetails, {}: FormikHelpers<ServerDetails>) => {
        clearFlashes('store:conf');
        setLoading(true);

        subscribe(parseInt(match.params.id), egg, name, description).then(data => {
            if (!data) return; else window.location.href = data;
        }).catch((error: AxiosError) => {
            addFlash({ key: 'store:conf', type: 'warning', title: 'FyreControl', message: httpErrorToHuman(error) });
        }).then(() => setLoading(false));
    };

    return (
        <PageContentBlock title={'Store'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'store:conf'} css={tw`mb-4`}/>
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered/>
                </div>
                :
                <div css={tw`w-full`}>
                    <Formik onSubmit={submit} initialValues={{ name: 'Fyre Instance', description: `${user.name_first}'s Instance` }} validationSchema={object().shape({ name: string().required(), description: string() })}>
                        <Form>
                            <div css={tw`grid gap-8 md:grid-cols-2`}>
                                <TitledGreyBox title={'Instance Name'} icon={faLayerGroup}>
                                    <div css={tw`px-1 py-2`}>
                                        <Field name={'name'}/>
                                    </div>
                                </TitledGreyBox>
                                <TitledGreyBox title={'Instance Description'} icon={faLayerGroup}>
                                    <div css={tw`px-1 py-2`}>
                                        <Field name={'description'}/>
                                    </div>
                                </TitledGreyBox>
                                <TitledGreyBox title={'Instance Type'} icon={faLayerGroup}>
                                    <div css={tw`px-1 py-2`}>
                                        <Select defaultValue={data[0].egg} name={'product'} onChange={x => setEgg(parseInt(x.target.value))}>
                                            {data.map((type: Type) => (
                                                <option value={type.egg}>{type.name}</option>
                                            ))}
                                        </Select>
                                    </div>
                                </TitledGreyBox>
                            </div>
                            <br/><br/>
                            <div css={tw`flex justify-end text-right`}>
                                <InputSpinner visible={loading}>
                                    <Button type={'submit'} disabled={loading}>Create Server</Button>
                                </InputSpinner>
                            </div>
                        </Form>
                    </Formik>
                </div>
            }
        </PageContentBlock>
    );
};
