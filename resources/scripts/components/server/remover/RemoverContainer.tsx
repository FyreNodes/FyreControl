import React, { useEffect, useState } from 'react';
import { ServerContext } from '@/state/server';
import useFlash from '@/plugins/useFlash';
import ServerContentBlock from '@/components/elements/ServerContentBlock';
import tw from 'twin.macro';
import useSWR from 'swr';
import FlashMessageRender from '@/components/FlashMessageRender';
import Spinner from '@/components/elements/Spinner';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import Label from '@/components/elements/Label';
import GreyRowBox from '@/components/elements/GreyRowBox';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faFile, faPlus, faTrash } from '@fortawesome/free-solid-svg-icons';
import getAutoRemoveFiles from '@/api/server/remover/getAutoRemoveFiles';
import { number, object, string } from 'yup';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';
import Select from '@/components/elements/Select';
import Field from '@/components/elements/Field';
import { Field as FormikField, Form, Formik, FormikHelpers } from 'formik';
import addRemovableFile from '@/api/server/remover/addRemovableFile';
import Button from '@/components/elements/Button';
import DeleteFileButton from '@/components/server/remover/DeleteFileButton';

export interface AutoRemoverResponse {
    files: any[],
}

interface CreateValues {
    file: string;
    day: string;
    hour: number;
    minute: number;
}

export default () => {
    const uuid = ServerContext.useStoreState(state => state.server.data!.uuid);

    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<AutoRemoverResponse>([ uuid, '/remover' ], key => getAutoRemoveFiles(key));

    const [ isSubmit, setSubmit ] = useState(false);

    const days = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    useEffect(() => {
        if (!error) {
            clearFlashes('server:remover');
        } else {
            clearAndAddHttpError({ key: 'server:remover', error });
        }
    }, [ error ]);

    const submit = ({ file, day, hour, minute }: CreateValues, { setSubmitting }: FormikHelpers<CreateValues>) => {
        clearFlashes('server:remover');
        setSubmitting(false);
        setSubmit(true);

        addRemovableFile(uuid, file, (day === '*' ? 1 : 2), day, hour, minute).then(() => {
            mutate().then();
            setSubmit(false);
        }).catch(error => {
            clearAndAddHttpError({ key: 'server:remover', error });
            setSubmitting(false);
            setSubmit(false);
        });
    };

    return (
        <ServerContentBlock title={'Cleanup'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'server:remover'} css={tw`mb-4`}/>
            </div>
            {!data ?
                (
                    <div css={tw`w-full`}>
                        <Spinner size={'large'} centered />
                    </div>
                )
                :
                (
                    <>
                        <div css={tw`w-full lg:w-8/12 mt-4 lg:mt-0`}>
                            {data.files.length < 1 ?
                                <p css={tw`text-center text-sm text-neutral-400 pt-4 pb-4`}>
                                    There are no specified files in this server.
                                </p>
                                :
                                (data.files.map((item, key) => (
                                    <GreyRowBox $hoverable={false} css={tw`flex-wrap md:flex-nowrap mt-2`} key={key}>
                                        <GreyRowBox $hoverable={false} css={tw`flex-wrap md:flex-nowrap mt-2`} key={key}>
                                            <div css={tw`flex items-center w-full md:w-auto`}>
                                                <div css={tw`pr-4 text-neutral-400`}>
                                                    <FontAwesomeIcon icon={faFile} />
                                                </div>
                                                <div css={tw`flex-1 w-64`}>
                                                    <span>{item.file}</span>
                                                    <Label>File</Label>
                                                </div>
                                                <div css={tw`flex-1 w-64 ml-4`}>
                                                    <span>{item.type.charAt(0) === '*' ? 'Every day at: ' + item.type.slice(1).replace('|', '') : days[item.type.charAt(0) - 1] + ' at: ' + item.type.slice(1).replace('|', '')}</span>
                                                    <Label>Type</Label>
                                                </div>
                                                <div css={tw`flex-1 w-16`}>
                                                    <span>{item.last_deleted === null ? 'Never' : item.last_deleted}</span>
                                                    <Label>Last Deleted</Label>
                                                </div>
                                            </div>
                                            <div css={tw`w-full md:flex-none md:w-32 md:text-center mt-4 md:mt-0 text-right`}>
                                                <DeleteFileButton fileId={item.id} onDeleted={() => mutate()}/>
                                            </div>
                                        </GreyRowBox>
                                    </GreyRowBox>
                                )))
                            }
                        </div>
                        <div css={tw`w-full lg:w-4/12 lg:pl-4`}>
                            <TitledGreyBox icon={faTrash} title={'Auto File Deletion'}>
                                <div css={tw`px-1 py-2`}>
                                    You can delete files every day or every week in specified time.
                                </div>
                            </TitledGreyBox>

                            <TitledGreyBox icon={faPlus} title={'Add File'} css={tw`mt-4`}>
                                <div css={tw`px-1 py-2`}>
                                    <Formik
                                        onSubmit={submit}
                                        initialValues={{ file: '', day: '*', hour: 0, minute: 0 }}
                                        validationSchema={object().shape({
                                            file: string().required().min(1).max(100),
                                            day: string().required(),
                                            hour: number().required().min(0).max(23),
                                            minute: number().required().min(0).max(59),
                                        })}
                                    >
                                        <Form>
                                            <div css={tw`flex flex-wrap`}>
                                                <div css={tw`mb-6 w-full`}>
                                                    <Field
                                                        name={'file'}
                                                        label={'File'}
                                                        placeholder={'log.txt'}
                                                    />
                                                </div>
                                                <div css={tw`mb-6 w-full`}>
                                                    <Label>Day</Label>
                                                    <FormikFieldWrapper name={'day'}>
                                                        <FormikField as={Select} name={'day'}>
                                                            <option value={'*'}>Everyday</option>
                                                            <option value={1}>Monday</option>
                                                            <option value={2}>Tuesday</option>
                                                            <option value={3}>Wednesday</option>
                                                            <option value={4}>Thursday</option>
                                                            <option value={5}>Friday</option>
                                                            <option value={6}>Saturday</option>
                                                            <option value={7}>Sunday</option>
                                                        </FormikField>
                                                    </FormikFieldWrapper>
                                                </div>
                                                <div css={tw`mb-6 w-full lg:w-1/2 lg:pr-4`}>
                                                    <Field
                                                        name={'hour'}
                                                        label={'Hour'}
                                                    />
                                                </div>
                                                <div css={tw`mb-6 w-full lg:w-1/2`}>
                                                    <Field
                                                        name={'minute'}
                                                        label={'Minute'}
                                                    />
                                                </div>
                                            </div>
                                            <div css={tw`flex justify-end`}>
                                                <Button type={'submit'} disabled={isSubmit}>Add</Button>
                                            </div>
                                        </Form>
                                    </Formik>
                                </div>
                            </TitledGreyBox>
                        </div>
                    </>
                )
            }
        </ServerContentBlock>
    );
};
