import React from 'react';
import PageContentBlock from "@/components/elements/PageContentBlock";
import FlashMessageRender from "@/components/FlashMessageRender";
import ContentBox from "@/components/elements/ContentBox";
import tw from "twin.macro";
import DiscordModal from "@/components/dashboard/account/integrations/DiscordModal";
import styled from "styled-components/macro";
import {breakpoint} from "@/theme";
import GitHubModal from "@/components/dashboard/account/integrations/GitHubModal";
import PageLabel from "@/components/elements/PageLabel";
import {faLink} from "@fortawesome/free-solid-svg-icons";

const Container = styled.div`
    ${tw`flex flex-wrap justify-around`};

    ${breakpoint('xl')`
        ${tw`w-auto flex-1`};
    `}
`;

export default () => {
    return (
        <PageContentBlock title={'Integrations • FyreNodes'}>
            <PageLabel icon={faLink} title={'Account Integrations'} description={'Manage linked account integrations.'}/>
            <FlashMessageRender byKey={'account:int'}/>
            <Container>
                <ContentBox title={'Discord'} css={tw`mt-8 sm:w-2/6 md:w-3/12 sm:mt-0`}>
                    <DiscordModal/>
                </ContentBox>
                <ContentBox title={'GitHub'} css={tw`mt-8 sm:w-2/6 md:w-3/12 sm:ml-4 sm:mt-0`}>
                    <GitHubModal/>
                </ContentBox>
                {/*<ContentBox title={'Discord 3'} css={tw`mt-8 sm:w-2/6 md:w-3/12 md:ml-4 md:mt-0`}>
                    <DiscordModal/>
                </ContentBox>
                <ContentBox title={'Discord 4'} css={tw`mt-8 sm:w-2/6 md:w-3/12 sm:ml-4 md:ml-0`}>
                    <DiscordModal/>
                </ContentBox>
                <ContentBox title={'Discord 5'} css={tw`mt-8 sm:w-2/6 md:w-3/12 md:ml-4`}>
                    <DiscordModal/>
                </ContentBox>
                <ContentBox title={'Discord 6'} css={tw`mt-8 sm:w-2/6 md:w-3/12 sm:ml-4`}>
                    <DiscordModal/>
                </ContentBox>*/}
            </Container>
        </PageContentBlock>
    )
};
