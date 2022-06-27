import React, { useEffect, useState } from 'react';
import tw from 'twin.macro';
import { ServerContext } from '@/state/server';
import { SocketEvent } from '@/components/server/events';
import Fade from '@/components/elements/Fade';
import { SwitchTransition } from 'react-transition-group';
import stripAnsi from 'strip-ansi';
import shareServerLog, { PasteResponse } from '@/api/server/shareServerLog';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faClipboard } from '@fortawesome/free-solid-svg-icons';
import { Toast, CMDButton, CMDButtonDisabled } from '@/assets/styles/McPasteStyle';

// fuck tailwind
const copyData = (content: string) => {
    function copyNavigator () {
        navigator.clipboard.writeText(content).then();
    }

    function copyHtml () {
        const area = document.createElement('textarea');
        area.value = content;
        area.style.position = 'fixed';
        document.body.appendChild(area);
        area.focus();
        area.select();
        area.setSelectionRange(0, 99999);
        document.execCommand('copy');
        document.body.removeChild(area);
    }

    if (navigator.clipboard !== undefined) copyNavigator();
    else copyHtml();
};

export default () => {
    const [ log, setLog ] = useState<string[]>([]);
    const addLog = (data: string) => setLog(prevLog => [ ...prevLog, data.startsWith('>') ? data.substring(1) : data ]);

    const [ uploading, setUploading ] = useState(false);
    const [ copied, setCopied ] = useState<false | PasteResponse>(false);
    const { connected, instance } = ServerContext.useStoreState(state => state.socket);

    const uuid = ServerContext.useStoreState(state => state.server.data!.uuid);

    useEffect(() => {
        if (!connected || !instance) return;

        instance.addListener(SocketEvent.CONSOLE_OUTPUT, addLog);

        return () => {
            instance.removeListener(SocketEvent.CONSOLE_OUTPUT, addLog);
        };
    }, [ connected, instance ]);

    const [ toastText, setToastText ] = useState('');

    useEffect(() => {
        if (!copied) return;
        if (!copied.key) return setToastText('An error has occured');
        setToastText('Copied to clipboard.');
    }, [ copied ]);

    const resetStateAfter = (ms = 2500) => {
        setTimeout(() => {
            setCopied(false);
            setUploading(false);
        }, ms);
    };

    const mcPaste = () => {
        if (uploading) return;
        const data = stripAnsi(log.map(it => it.replace('\r', '')).join('\n')) || '';
        setUploading(true);
        shareServerLog(uuid, data)
            .then((response: PasteResponse): PasteResponse => {
                if (response.key) {
                    copyData(`https://bin.fyrenodes.net/${response.key}.txt`);
                }
                return response;
            }).then((response) => {
                setCopied(response);
                resetStateAfter();
            })
            .catch((err) => {
                console.log(err);
                setCopied({ error: 'Unexpected error....' });
                resetStateAfter();
            });
    };

    const CMDButtonType = uploading ? CMDButtonDisabled : CMDButton;

    const content
        = (
            <CMDButtonType onClick={() => mcPaste()}>
                <div css={[ tw`flex-shrink-0 p-2 font-bold`, uploading ? '' : tw`cursor-pointer` ]}>
                    <FontAwesomeIcon icon={faClipboard} fixedWidth size={'lg'} color={'#9aa5b1'}/>
                </div>
            </CMDButtonType>
        );

    return (
        <div>
            { content }
            <SwitchTransition>
                <Fade timeout={250} key={copied ? 'visible' : 'invisible'}>
                    {copied ?
                        <Toast>
                            <div>
                                <p>{toastText}</p>
                            </div>
                        </Toast>
                        : <></>
                    }
                </Fade>
            </SwitchTransition>
        </div>
    );
};
