import React, { useEffect, useRef, useState } from 'react';
import { Link, RouteComponentProps } from 'react-router-dom';
import login from '@/api/auth/login';
import LoginFormContainer from '@/components/auth/LoginFormContainer';
import { useStoreState } from 'easy-peasy';
import { Formik, FormikHelpers } from 'formik';
import { object, string } from 'yup';
import Field from '@/components/elements/Field';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSignInAlt } from '@fortawesome/free-solid-svg-icons';
import tw from 'twin.macro';
import Button from '@/components/elements/Button';
import Reaptcha from 'reaptcha';
import useFlash from '@/plugins/useFlash';
import Particles from 'react-tsparticles';
import { loadFull } from "tsparticles";
import styled from "styled-components/macro";
import { construct as discordConstruct } from "@/api/auth/integrations/discord";
import { construct as githubConstruct } from '@/api/auth/integrations/github';
import AuthenticationContainer from "@/components/auth/AuthenticationContainer";

interface Values {
    username: string;
    password: string;
}

const Header = styled.div`
    & {
        ${tw`w-5/7 my-0 mx-auto h-24 min-h-0 flex items-center justify-center md:justify-between relative`};
    }
`;

const HeaderButton = styled.div`
    & {
        ${tw`bg-blue-600 rounded text-white font-bold uppercase text-xs cursor-pointer py-3.5 px-7 tracking-wider transition-all duration-500 shadow-md hover:shadow-lg`};
    }
`;

const LoginContainer = ({ history }: RouteComponentProps) => {
    const ref = useRef<Reaptcha>(null);
    const [ token, setToken ] = useState('');

    const { clearFlashes, clearAndAddHttpError, addFlash } = useFlash();
    const { enabled: recaptchaEnabled, siteKey } = useStoreState(state => state.settings.data!.recaptcha);
    const appVersion: string = useStoreState(state => state.settings.data!.version);

    useEffect(() => { clearFlashes(''); }, []);
    useEffect(() => { document.title = 'Login • FyreNodes'; }, []);

    const onSubmit = (values: Values, { setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes('');

        if (recaptchaEnabled && !token) {
            ref.current!.execute().catch(error => {
                console.error(error);
                setSubmitting(false);
                clearAndAddHttpError({ error });
            });
            return;
        }

        login({ ...values, recaptchaData: token })
            .then(response => {
                if (response.complete) {
                    // @ts-ignore
                    window.location = response.intended || '/';
                    return;
                }

                history.replace('/auth/login/checkpoint', { token: response.confirmationToken });
            })
            .catch(error => {
                console.error(error);
                setToken('');
                if (ref.current) ref.current.reset().then();
                setSubmitting(false);
                clearAndAddHttpError({ error });
            });
    };

    const discordSubmit = () => {
        clearFlashes('');
        discordConstruct().then(data => {
            if (!data.success) return addFlash({ title: 'FyreControl', type: 'warning', message: 'The Discord integration is currently disabled.' });
            window.location.href = data.url;
        });
    };

    const githubSubmit = () => {
        clearFlashes('');
        githubConstruct().then(data => {
            if (!data.success) return addFlash({ title: 'FyreControl', type: 'warning', message: 'The GitHub integration is currently disabled.' });
            window.location.href = data.url;
        });
    };

    return (
        <AuthenticationContainer>
            <Formik onSubmit={onSubmit} initialValues={{ username: '', password: '' }} validationSchema={object().shape({ username: string().required('A username or email must be provided.'), password: string().required('Please enter your account password.') })}>
                {({ isSubmitting, setSubmitting, submitForm }) => (
                    <>
                        <LoginFormContainer title={'Login with your FyreID'} version={appVersion} css={tw`w-full flex mx-auto w-3/4 text-white`}>
                            <React.Fragment>
                                <Field light type={'text'} label={'Username or Email'} name={'username'} disabled={isSubmitting}/>
                                <div css={tw`mt-6`}>
                                    <Field light type={'password'} label={'Password'} name={'password'} disabled={isSubmitting}/>
                                </div>
                                <div css={tw`mt-6 text-white flex justify-end`}>
                                    <Button type={'submit'} size={'small'} color={'primary'} isLoading={isSubmitting} disabled={isSubmitting}><FontAwesomeIcon icon={faSignInAlt}/> Login</Button>
                                </div>
                                {recaptchaEnabled &&
                                    <Reaptcha ref={ref} size={'invisible'} sitekey={siteKey || '_invalid_key'} onVerify={response => {setToken(response); submitForm().then()}} onExpire={() => {setSubmitting(false); setToken('')}}/>
                                }
                                <div css={tw`mt-6 text-center tracking-wide no-underline text-xs`}>
                                    <Link to={'/auth/register'} css={tw`hover:text-neutral-300`}>Need an account?</Link>
                                    &nbsp;&nbsp;
                                    <Link to={'/auth/password'} css={tw`hover:text-neutral-300`}>Forgot password?</Link>
                                </div>
                                <div css={tw`text-center`}>
                                    <span css={tw`font-bold`}>――――――――――――――</span>
                                    <br/>
                                    <br/>
                                </div>
                                <div css={tw`text-sm text-neutral-500 text-center`}>
                                    <a onClick={() => discordSubmit()} css={tw`cursor-pointer`}>
                                        <img css={tw`inline-block w-12 mx-2 mb-2`} src={'/assets/svgs/discord.svg'} alt={'Discord'}/>
                                    </a>
                                    <a onClick={() => githubSubmit()} css={tw`cursor-pointer`}>
                                        <img css={tw`inline-block w-12 mx-2 mb-2`} src={'/assets/svgs/github.svg'} alt={'GitHub'}/>
                                    </a>
                                </div>
                            </React.Fragment>
                        </LoginFormContainer>
                    </>
                )}
            </Formik>
        </AuthenticationContainer>
    );
};

export default LoginContainer;
