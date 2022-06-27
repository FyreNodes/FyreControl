import styled from 'styled-components/macro';
import tw, { theme } from 'twin.macro';

export const NavigationStyle = styled.div`
    ${tw`w-full shadow-md overflow-x-visible`};
    background-color: var(--header-color);
    
    & > div {
        ${tw`mx-auto w-full flex items-center`};
    }
    
    & #logo {
        ${tw`flex-1`};
        
        & > a {
            ${tw`text-2xl font-header px-4 no-underline text-neutral-200 hover:text-neutral-100 transition-colors duration-150`};
        }
    }
`;

export const RightNavigationStyle = styled.div`
    ${tw`flex h-full items-center justify-center`};
    
    & > a, & > button, & > .navigation-link {
        ${tw`flex items-center h-full no-underline text-neutral-300 px-6 cursor-pointer transition-all duration-150`};
        
        &:active, &:hover {
            ${tw`text-neutral-100`};
            background-color: var(--primary-color);
        }
        
        &:active, &:hover, &.active {
            box-shadow: inset 0 -2px ${theme`colors.cyan.700`.toString()};
        }
    }
`;

export const Dropdown = styled.div`
    ${tw`flex items-center flex-shrink-0`};
`;

export const DropdownUser = styled.div`
    ${tw`relative inline-block text-sm cursor-pointer text-white mr-1.5 ml-3`};
    
    & > svg {
        ${tw`flex-shrink-0`};
    }
`;

export const DropdownContent = styled.div`
    ${tw`block absolute z-50 cursor-pointer pl-0.5 rounded-xl top-8`};
    background-color: var(--secondary-color);
    min-width: 150px;
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    
    & > a, & > button {
        ${tw`block no-underline py-2 px-2.5 hover:text-white`};
        color: rgba(154.148,165.363,177.352,var(--tw-text-opacity));
    }
`;
