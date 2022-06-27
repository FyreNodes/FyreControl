import React, { useEffect } from 'react';
import ContentContainer from '@/components/elements/ContentContainer';
import { CSSTransition } from 'react-transition-group';
import tw from 'twin.macro';
import FlashMessageRender from '@/components/FlashMessageRender';

export interface PageContentBlockProps {
    title?: string;
    className?: string;
    showFlashKey?: string;
}

const PageContentBlock: React.FC<PageContentBlockProps> = ({ title, showFlashKey, className, children }) => {
    useEffect(() => {
        if (title) {
            document.title = title;
        }
    }, [ title ]);

    return (
        <CSSTransition timeout={150} classNames={'fade'} appear in>
            <>
                <ContentContainer css={tw`my-4 sm:my-10`} className={className}>
                    {showFlashKey &&
                    <FlashMessageRender byKey={showFlashKey} css={tw`mb-4`}/>
                    }
                    {children}
                </ContentContainer>
                <ContentContainer css={tw`text-xs text-center`}>
                    <p css={tw`my-5 text-neutral-500 sm:float-left`}>Copyright &copy; 2021-{(new Date()).getFullYear()}&nbsp;<a rel={'nofollow noreferrer'} href={'https://fyrenodes.com'} target={'_blank'} css={tw`no-underline text-neutral-500 hover:text-neutral-300`}>FyreNodes LTD</a>. All rights reserved.</p>
                    <p css={tw`my-5 text-neutral-500 mt-2 sm:mt-0 sm:float-right`}>
                        <a href={'https://fyrenodes.com/terms'} rel={'nofollow noreferrer'} css={tw`hover:text-neutral-300`}>Terms of Service</a>
                        &nbsp;&nbsp;&nbsp;
                        <a href={'https://fyrenodes.com/privacy'} rel={'nofollow noreferrer'} css={tw`hover:text-neutral-300`}>Privacy Policy</a>
                        &nbsp;&nbsp;&nbsp;
                        <a href={'https://fyrenodes.com/discord'} rel={'nofollow noreferrer'} css={tw`hover:text-neutral-300`}>Contact</a>
                    </p>
                </ContentContainer>
            </>
        </CSSTransition>
    );
};

export default PageContentBlock;
