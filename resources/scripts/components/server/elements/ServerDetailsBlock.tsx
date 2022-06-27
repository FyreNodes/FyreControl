import React, { useEffect, useState } from 'react';
import tw from 'twin.macro';
import { faCloud, faCode, faEthernet, faServer, faCircle } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import { ServerContext } from '@/state/server';
import CopyOnClick from '@/components/elements/CopyOnClick';
import statusToColor from '@/components/functions/statusToColor';
import PowerControls from '@/components/server/elements/PowerControls';
import UptimeDuration from '@/components/server/elements/stats/UptimeDuration';
import { SocketEvent, SocketRequest } from '@/components/server/events';
import { formatIp } from '@/helpers';

type Stats = Record<'uptime', number>;

const ServerDetailsBlock = () => {
    const status = ServerContext.useStoreState(state => state.status.value);
    const serverName = ServerContext.useStoreState(state => state.server.data!.name);
    const isInstalling = ServerContext.useStoreState(state => state.server.data!.isInstalling);
    const isTransferring = ServerContext.useStoreState(state => state.server.data!.isTransferring);
    const nodeName = ServerContext.useStoreState(state => state.server.data!.node);
    const serverID = ServerContext.useStoreState(state => state.server.data!.id);
    const primaryAllocation = ServerContext.useStoreState(state => state.server.data!.allocations.filter(alloc => alloc.isDefault).map(allocation => (allocation.alias || formatIp(allocation.ip)) + ':' + allocation.port)).toString();
    const connected = ServerContext.useStoreState(state => state.socket.connected);
    const instance = ServerContext.useStoreState(state => state.socket.instance);
    const [ stats, setStats ] = useState<Stats>({ uptime: 0 });

    const statsListener = (data: string) => {
        let stats: any = {};
        try {
            stats = JSON.parse(data);
        } catch (e) {
            return;
        }

        setStats({
            uptime: stats.uptime || 0,
        });
    };

    useEffect(() => {
        if (!connected || !instance) {
            return;
        }

        instance.addListener(SocketEvent.STATS, statsListener);
        instance.send(SocketRequest.SEND_STATS);

        return () => {
            instance.removeListener(SocketEvent.STATS, statsListener);
        };
    }, [ instance, connected ]);

    return (
        <TitledGreyBox css={tw`break-words mb-4`} title={serverName} icon={faServer}>
            <p css={tw`text-xs uppercase`}>
                <FontAwesomeIcon icon={faCircle} fixedWidth css={[ tw`mr-1`, statusToColor(status, isInstalling || isTransferring) ]}/>
                {!status ? 'Connecting...' : (isInstalling ? 'Installing' : (isTransferring) ? 'Transferring' : status)}
                {stats.uptime > 0 &&
                <span css={tw`ml-2 lowercase`}>
                    (<UptimeDuration uptime={stats.uptime / 1000}/>)
                </span>
                }
            </p>
            <CopyOnClick text={primaryAllocation}>
                <p css={tw`text-xs mt-2`}>
                    <FontAwesomeIcon icon={faEthernet} fixedWidth css={tw`mr-1`}/>{primaryAllocation}
                </p>
            </CopyOnClick>
            <CopyOnClick text={nodeName}>
                <p css={tw`text-xs mt-2`}>
                    <FontAwesomeIcon icon={faCloud} fixedWidth css={tw`mr-1`}/>Node:
                    <span css={tw`ml-1`}>{nodeName}</span>
                </p>
            </CopyOnClick>
            <CopyOnClick text={serverID}>
                <p css={tw`text-xs mt-2`}>
                    <FontAwesomeIcon icon={faCode} fixedWidth css={tw`mr-1`}/>ID:
                    <span css={tw`ml-1`}>{serverID}</span>
                </p>
            </CopyOnClick>
            <PowerControls/>
        </TitledGreyBox>
    );
};

export default ServerDetailsBlock;
