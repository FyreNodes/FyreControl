import React, { useEffect, useState } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import getStaffRequests from '@/api/staff/getStaffRequests';
import useFlash from '@/plugins/useFlash';
import tw from 'twin.macro';
import useSWR from 'swr';
import Spinner from '@/components/elements/Spinner';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import { Field as FormikField, Form, Formik, FormikHelpers } from 'formik';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';
import Button from '@/components/elements/Button';
import makeStaffRequest from '@/api/staff/makeStaffRequest';
import { number, object, string } from 'yup';
import Field from '@/components/elements/Field';
import Label from '@/components/elements/Label';
import Select from '@/components/elements/Select';
import GreyRowBox from '@/components/elements/GreyRowBox';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faServer } from '@fortawesome/free-solid-svg-icons';
import styled from 'styled-components/macro';
import { Link } from 'react-router-dom';
import DeleteStaffRequestButton from '@/components/dashboard/staff/DeleteStaffRequestButton';
import FlashMessageRender from '@/components/FlashMessageRender';

const Code = styled.code`${tw`font-mono py-1 px-2 bg-neutral-900 rounded text-sm inline-block`}`;

export interface StaffRequestResponse {
    servers: any[];
    requests: any[];
}

interface CreateValues {
    server: number;
    message: string;
}

export default () => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<StaffRequestResponse>([ '/staff' ], () => getStaffRequests());
    const [ isSubmit, setSubmit ] = useState(false);

    useEffect(() => {
        if (!error) {
            clearFlashes('staff');
        } else {
            clearAndAddHttpError({ key: 'staff', error });
        }
    });

    const submit = ({ server, message }: CreateValues, { setSubmitting }: FormikHelpers<CreateValues>) => {
        clearFlashes('staff');
        clearFlashes('staff:create');
        setSubmitting(false);
        setSubmit(true);

        makeStaffRequest(server, message).then(() => {
            mutate().then();
            setSubmit(false);
        }).catch(error => {
            setSubmitting(false);
            setSubmit(false);
            clearAndAddHttpError({ key: 'staff:create', error });
        });
    };

    return (
        <PageContentBlock title={'Staff System • FyreNodes'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'staff'} css={tw`mb-4`} />
            </div>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'staff:create'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    <div css={tw`w-full lg:w-8/12 mt-4 lg:mt-0`}>
                        <TitledGreyBox title={'Request Access'}>
                            <div css={tw`px-1 py-2`}>
                                <Formik
                                    onSubmit={submit}
                                    initialValues={{ server: data.servers[0]?.id, message: '' }}
                                    validationSchema={object().shape({
                                        server: number().required(),
                                        message: string().required(),
                                    })}
                                >
                                    <Form>
                                        <div css={tw`flex flex-wrap`}>
                                            <div css={tw`mb-6 w-full lg:w-1/2`}>
                                                <Label>Server</Label>
                                                <FormikFieldWrapper name={'Server'}>
                                                    <FormikField as={Select} name={'server'}>
                                                        {data.servers.map((item, key) => (
                                                            <option key={key} value={item.id}>{item.name} - {item.uuidShort}</option>
                                                        ))}
                                                    </FormikField>
                                                </FormikFieldWrapper>
                                            </div>
                                            <div css={tw`mb-6 w-full lg:w-1/2 lg:pl-4`}>
                                                <Field
                                                    name={'message'}
                                                    label={'Message'}
                                                />
                                            </div>
                                        </div>
                                        <div css={tw`flex justify-end`}>
                                            <Button type={'submit'} disabled={isSubmit}>Request Access</Button>
                                        </div>
                                    </Form>
                                </Formik>
                            </div>
                        </TitledGreyBox>

                        {data.requests.length < 1 ?
                            <p css={tw`text-center text-sm text-neutral-400 pt-4 pb-4`}>
                                There are no requests.
                            </p>
                            :
                            (data.requests.map((item, key) => (
                                <GreyRowBox $hoverable={false} css={tw`flex-wrap md:flex-nowrap mt-2`} key={key}>
                                    <GreyRowBox $hoverable={false} css={tw`flex-wrap md:flex-nowrap mt-2`} key={key}>
                                        <div css={tw`flex items-center w-full md:w-auto`}>
                                            <div css={tw`pr-2 text-neutral-400`}>
                                                <FontAwesomeIcon icon={faServer} />
                                            </div>
                                            <div css={tw`flex-1 md:w-64`}>
                                                <Link to={`/server/${item.server.uuidShort}`}>
                                                    <Code>{item.server.name}</Code>
                                                </Link>
                                                <Label>Server Name</Label>
                                            </div>
                                            <div css={tw`flex-1 md:w-32`}>
                                                <Code>{item.status}</Code>
                                                <Label>Status</Label>
                                            </div>
                                            <div css={tw`flex-1 md:w-48`}>
                                                <Code>{item.updated_at}</Code>
                                                <Label>Updated</Label>
                                            </div>
                                        </div>
                                        <div css={tw`w-full md:flex-none md:w-32 md:text-center mt-4 md:mt-0 text-right`}>
                                            <DeleteStaffRequestButton id={item.id} onDeleted={() => mutate()}/>
                                        </div>
                                    </GreyRowBox>
                                </GreyRowBox>
                            )))
                        }
                    </div>
                    <div css={tw`w-full lg:w-4/12 lg:pl-4`}>
                        <TitledGreyBox title={'Request Help'}>
                            <div css={tw`px-1 py-2`}>
                                You can send requests to server owners that you want to access the server. The owner can
                                accept or deny it.
                            </div>
                        </TitledGreyBox>
                    </div>
                </>
            }
        </PageContentBlock>
    );
};
