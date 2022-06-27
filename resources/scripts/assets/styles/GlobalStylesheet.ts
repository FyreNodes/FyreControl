import tw from 'twin.macro';
import { createGlobalStyle } from 'styled-components/macro';

export interface Styles {
    headerColor: string;
    primaryColor: string;
    secondaryColor: string;
    themeColor: string;
    bgColor: string;
    consoleColor: string;
    iconColor: string;
    dropdownColor: string;
    hoverColor: string;
}

export const theme: Styles = {
    headerColor: '#0E0E23',
    primaryColor: '#161933',
    secondaryColor: '#282A36',
    themeColor: '#1CB1Df',
    bgColor: '#10122B',
    consoleColor: '#0B1013',
    iconColor: '#146871',
    dropdownColor: '#111229',
    hoverColor: '#14203d'
};

export default createGlobalStyle`
    :root {
        --header-color: ${theme.headerColor};
        --primary-color: ${theme.primaryColor};
        --secondary-color: ${theme.secondaryColor};
        --theme-color: ${theme.themeColor};
        --bg-color: ${theme.bgColor};
        --console-color: ${theme.consoleColor};
        --icon-color: ${theme.iconColor};
        --dropdown-color: ${theme.dropdownColor};
        --hover-color: ${theme.hoverColor};
        --text-color: #fafafa !important;
        --tw-text-opacity: 1 !important;
        --hover-outline-color: #1cb1df;
    }
    
    html {
        scrollbar-color: var(--theme-color) transparent;
        scrollbar-width: ${navigator.userAgent.match(/firefox|fxios/i) && navigator.appVersion.includes('Win') ? 'thin' : 'unset'};
    }
    
    body {
        ${tw`font-sans text-neutral-200`};
        background-color: var(--bg-color);
        letter-spacing: 0.015em;
    }

    h1, h2, h3, h4, h5, h6 {
        ${tw`font-medium tracking-normal font-header`};
    }

    p {
        ${tw`text-neutral-200 leading-snug font-sans`};
    }

    form {
        ${tw`m-0`};
    }

    textarea, select, input, button, button:focus, button:focus-visible {
        ${tw`outline-none`};
    }

    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none !important;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield !important;
    }

    ::-webkit-scrollbar {
        background: none;
        width: 16px;
        height: 16px;
    }

    ::-webkit-scrollbar-thumb {
        border: solid 0 rgb(0 0 0 / 0%);
        border-right-width: 4px;
        border-left-width: 4px;
        -webkit-border-radius: 9px 4px;
        -webkit-box-shadow: inset 0 0 0 1px var(--theme-color), inset 0 0 0 4px var(--theme-color);
    }

    ::-webkit-scrollbar-track-piece {
        margin: 4px 0;
    }

    ::-webkit-scrollbar-thumb:horizontal {
        border-right-width: 0;
        border-left-width: 0;
        border-top-width: 4px;
        border-bottom-width: 4px;
        -webkit-border-radius: 4px 9px;
    }

    ::-webkit-scrollbar-thumb:hover {
        -webkit-box-shadow: inset 0 0 0 1px var(--theme-color), inset 0 0 0 4px var(--theme-color);
    }

    ::-webkit-scrollbar-corner {
        background: transparent;
    }
    
    .CodeMirror, .CodeMirror-gutters {
        background-color: var(--console-color) !important;
    }
`;
