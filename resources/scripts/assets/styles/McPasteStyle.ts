import tw from 'twin.macro';
import Button from '@/components/elements/Button';
import styled, { keyframes } from 'styled-components/macro';

export const fade = keyframes`
    from { opacity: 0 }
    to { opacity: 1 }
`;

export const Toast = styled.div`
    ${tw`fixed z-50 bottom-0 left-0 mb-4 w-full flex justify-end pr-4`};
    animation: ${fade} 250ms linear;

    & > div {
        ${tw`rounded px-4 py-2 text-white bg-neutral-900 border border-black opacity-75`};
    }
`;

export const CMDButton = styled.div`
    ${tw`p-1 transition-all rounded-br cursor-pointer`};

    &:hover {
        background-color: #383a44;
    }
`;

export const CMDButtonDisabled = styled.div`
    ${tw`p-1 transition-all rounded-br`}
`;

styled(Button)`
    ${tw`transition-all`};
    background-color: #ffffff;

    &:hover {
        background-color: #383a44;
    }
`;
