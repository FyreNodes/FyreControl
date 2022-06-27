import React from 'react';
import tw from 'twin.macro';
import Can from '@/components/elements/Can';
import Button from '@/components/elements/Button';
import StopOrKillButton from '@/components/server/elements/StopOrKillButton';
import { PowerAction } from '@/components/server/ServerConsole';
import { ServerContext } from '@/state/server';

const PowerControls = () => {
    const status = ServerContext.useStoreState(state => state.status.value);
    const instance = ServerContext.useStoreState(state => state.socket.instance);
    const isInstalling = ServerContext.useStoreState(state => state.server.data!.isInstalling);
    const isTransferring = ServerContext.useStoreState(state => state.server.data!.isTransferring);

    const sendPowerCommand = (command: PowerAction) => {
        instance && instance.send('set state', command);
    };

    return (
        <>
            {isInstalling ? <></> : isTransferring ? <></> :
                <>
                    <div css={tw`px-3 pt-3 flex text-xs justify-center`}>
                        <Can action={'control.start'}>
                            <Button size={'xsmall'} color={'green'} css={tw`mr-2`} disabled={status !== 'offline'} onClick={e => { e.preventDefault(); sendPowerCommand('start'); }}>Start</Button>
                        </Can>
                        <Can action={'control.restart'}>
                            <Button size={'xsmall'} color={'primary'} css={tw`mr-2`} disabled={status !== 'running'} onClick={e => { e.preventDefault(); sendPowerCommand('restart'); }}>Restart</Button>
                        </Can>
                        <Can action={'control.stop'}>
                            <StopOrKillButton onPress={action => sendPowerCommand(action)}/>
                        </Can>
                    </div>
                </>
            }
        </>
    );
};

export default PowerControls;
