import React, { forwardRef } from 'react';
import { Form } from 'formik';
import styled from 'styled-components/macro';
import { breakpoint } from '@/theme';
import FlashMessageRender from '@/components/FlashMessageRender';
import tw from 'twin.macro';

type Props = React.DetailedHTMLProps<React.FormHTMLAttributes<HTMLFormElement>, HTMLFormElement> & {
    title?: string;
    version: string;
}

const Container = styled.div`
    ${breakpoint('sm')`
        ${tw`w-4/5 mx-auto`}
    `};

    ${breakpoint('md')`
        ${tw`p-10`}
    `};

    ${breakpoint('lg')`
        ${tw`w-3/5`}
    `};

    ${breakpoint('xl')`
        ${tw`w-full`}
        max-width: 700px;
    `};
`;

export default forwardRef<HTMLFormElement, Props>(({ title, version, ...props }, ref) => (
    <div>
        <Container>
            {title &&
            <h2 css={tw`text-3xl text-center text-neutral-100 font-medium py-4`}>
                {title}
            </h2>
            }
            <FlashMessageRender css={tw`mb-2 px-1`}/>
            <Form {...props} ref={ref}>
                <div css={`background-color: var(--primary-color); ${tw`md:flex w-full shadow-lg rounded-lg p-6 mx-1 z-10`}`}>
                    <div css={tw`flex-1`}>
                        {props.children}
                    </div>
                </div>
            </Form>
            {/*<div css={tw`text-center no-underline text-xs mt-4`}>
                <p css={tw`text-neutral-500`}>Copyright &copy; 2021-{(new Date()).getFullYear()}&nbsp;<a rel={'nofollow noreferrer'} href={'https://fyrenodes.com'} target={'_blank'} css={tw`hover:text-neutral-200`}>FyreNodes LTD</a>. All rights reserved.</p>
                <p css={tw`text-neutral-500 mt-2`}>FyreControl - v{version}</p>
            </div>*/}
        </Container>
    </div>
));
