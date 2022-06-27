import styled from 'styled-components/macro';
import tw from 'twin.macro';

export default styled.div<{ $hoverable?: boolean }>`
    ${tw`flex rounded no-underline text-neutral-200 items-center p-4 border border-transparent transition-colors duration-150 overflow-hidden`};
    background-color: var(--primary-color);
        
    ${props => props.$hoverable !== false && '&:hover { border-color: var(--hover-outline-color); }'};

    & .icon {
        background-color: var(--icon-color); ${tw`rounded-full p-3`};
    }
`;
