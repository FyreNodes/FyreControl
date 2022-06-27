import tw, { theme as th } from 'twin.macro';
import { ITerminalOptions } from 'xterm';
import styled from 'styled-components/macro';
import { theme as style } from './GlobalStylesheet';

const theme: Record<string, unknown> = {
    background: style.consoleColor.toString(),
    cursor: 'transparent',
    black: th`colors.black`.toString(),
    red: '#E54B4B',
    green: '#9ECE58',
    yellow: '#FAED70',
    blue: '#396FE2',
    magenta: '#BB80B3',
    cyan: '#2DDAFD',
    white: '#d0d0d0',
    brightBlack: 'rgba(255, 255, 255, 0.2)',
    brightRed: '#FF5370',
    brightGreen: '#C3E88D',
    brightYellow: '#FFCB6B',
    brightBlue: '#82AAFF',
    brightMagenta: '#C792EA',
    brightCyan: '#89DDFF',
    brightWhite: '#ffffff',
    selection: '#FAF089',
};

export const terminalProps: ITerminalOptions = {
    disableStdin: true,
    cursorStyle: 'underline',
    allowTransparency: true,
    fontSize: 12,
    rows: 30,
    theme: theme,
};

export const TerminalDiv = styled.div`
    &::-webkit-scrollbar {
        ${tw`w-2`};
    }

    &::-webkit-scrollbar-thumb {
        ${tw`bg-neutral-900`};
    }
`;

export const CommandInput = styled.input`
    ${tw`text-sm transition-colors duration-150 px-2 bg-transparent border-0 border-b-2 border-transparent text-neutral-100 p-2 pl-0 w-full focus:ring-0`}
    &:focus {
        ${tw`border-cyan-700`};
    }
`;
