import React, { useEffect, useRef, useState } from 'react';
import { Link, RouteComponentProps } from 'react-router-dom';
import register from '@/api/auth/register';
import RegisterFormContainer from '@/components/auth/RegisterFormContainer';
import { useStoreState } from 'easy-peasy';
import { Formik, FormikHelpers } from 'formik';
import { object, string } from 'yup';
import Field from '@/components/elements/Field';
import tw from 'twin.macro';
import Button from '@/components/elements/Button';
import Reaptcha from 'reaptcha';
import useFlash from '@/plugins/useFlash';
import Particles from 'react-tsparticles';
import { loadFull } from "tsparticles";
import AuthenticationContainer from "@/components/auth/AuthenticationContainer";

interface Values {
    username: string;
    email: string;
    NameFirst: string;
    NameLast: string;
    password: string;
}

const RegisterContainer = ({ history }: RouteComponentProps) => {
    const ref = useRef<Reaptcha>(null);
    const [ token, setToken ] = useState('');

    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { enabled: recaptchaEnabled, siteKey } = useStoreState(state => state.settings.data!.recaptcha);

    useEffect(() => { clearFlashes(); }, []);
    useEffect(() => { document.title = 'Register • FyreNodes'; }, []);

    const onSubmit = (values: Values, { setSubmitting }: FormikHelpers<Values>) => {
        console.log(values);
        clearFlashes();

        if (recaptchaEnabled && !token) {
            ref.current!.execute().catch(error => {
                console.error(error);
                setSubmitting(false);
                clearAndAddHttpError({ error });
            });
            return;
        }

        register({ ...values, recaptchaData: token })
            .then(response => {
                if (response.complete) {
                    history.replace('/auth/login/');
                    return;
                }
                history.replace('/auth/register/');
            })
            .catch(error => {
                console.error(error);
                setToken('');
                if (ref.current) ref.current.reset().then();
                setSubmitting(false);
                clearAndAddHttpError({ error });
            });
    };

    return (
        <AuthenticationContainer>
            <Formik onSubmit={onSubmit} initialValues={{ username: '', email: '', NameFirst: '', NameLast: '', password: '' }} validationSchema={object().shape({ username: string().required('A username must be provided.'), email: string().required('An email address must be provided.'), NameFirst: string().required('A first name must be provided.'), NameLast: string().required('A last name must be provided.'), password: string().required('A password must be provided.') })}>
                {({ isSubmitting, setSubmitting, submitForm }) => (
                    <RegisterFormContainer title={'Create a new FyreID'} css={tw`w-full flex self-center ml-20 w-3/4 text-white`}>
                        <div css={tw`flex flow-root`}>
                            <div css={tw`w-15/32 float-left`}>
                                <Field light type={'text'} label={'First Name'} name={'NameFirst'} disabled={isSubmitting}/>
                            </div>
                            <div css={tw`w-15/32 float-right`}>
                                <Field light type={'text'} label={'Last Name'} name={'NameLast'} disabled={isSubmitting}/>
                            </div>
                        </div>
                        <div css={tw`mt-6`}>
                            <Field light type={'text'} label={'Username'} name={'username'} disabled={isSubmitting}/>
                        </div>
                        <div css={tw`mt-6`}>
                            <Field light type={'email'} label={'Email Address'} name={'email'} disabled={isSubmitting}/>
                        </div>
                        <div css={tw`mt-6`}>
                            <Field light type={'password'} label={'Password'} name={'password'} disabled={isSubmitting}/>
                        </div>
                        <div css={tw`mt-6`}>
                            <Button type={'submit'} size={'xlarge'} isLoading={isSubmitting} disabled={isSubmitting}>
                                Register
                            </Button>
                        </div>
                        {recaptchaEnabled &&
                            <Reaptcha ref={ref} size={'invisible'} sitekey={siteKey || '_invalid_key'}
                                      onVerify={response => {
                                          setToken(response);
                                          submitForm().then();
                                      }}
                                      onExpire={() => {
                                          setSubmitting(false);
                                          setToken('');
                                      }}
                            />
                        }
                        <div css={tw`mt-6 text-center`}>
                            <Link to={'/auth/login'} css={tw`text-xs text-neutral-500 tracking-wide no-underline uppercase hover:text-neutral-600`}>
                                Have an account?
                            </Link>
                        </div>
                    </RegisterFormContainer>
                )}
            </Formik>
        </AuthenticationContainer>
    );
};

export default RegisterContainer;
