import { useEffect, useState } from 'react';
import { ServerContext } from '@/state/server';
import { bytesToHuman, megabytesToHuman } from '@/helpers';
import { SocketEvent, SocketRequest } from '@/components/server/events';

export interface Stats {
    memory: string;
    cpu: string;
    disk: string;
    tx: string;
    rx: string;
    uptime: string;
    diskLimit: string;
    cpuLimit: string;
    memoryLimit: string;
}

export type StatsState = Record<'memory' | 'cpu' | 'disk' | 'tx' | 'rx' | 'uptime', number>

export default (): Stats => {
    const connected = ServerContext.useStoreState(state => state.socket.connected);
    const instance = ServerContext.useStoreState(state => state.socket.instance);
    const limits = ServerContext.useStoreState(state => state.server.data!.limits);
    const [ stats, setStats ] = useState<StatsState>({ memory: 0, cpu: 0, disk: 0, uptime: 0, tx: 0, rx: 0 });

    const statsListener = (data: string) => {
        let stats: any = {};
        try {
            stats = JSON.parse(data);
        } catch (e) {
            return;
        }

        setStats({
            memory: stats.memory_bytes,
            cpu: stats.cpu_absolute,
            disk: stats.disk_bytes,
            tx: stats.network.tx_bytes,
            rx: stats.network.rx_bytes,
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

    return {
        memory: bytesToHuman(stats.memory),
        cpu: stats.cpu.toFixed(2),
        disk: bytesToHuman(stats.disk),
        tx: bytesToHuman(stats.tx),
        rx: bytesToHuman(stats.rx),
        uptime: (stats.uptime / 1000).toString(),
        diskLimit: limits.disk ? megabytesToHuman(limits.disk) : 'Unlimited',
        cpuLimit: limits.cpu ? limits.cpu + '%' : 'Unlimited',
        memoryLimit: limits.memory ? megabytesToHuman(limits.memory) : 'Unlimited',
    };
};
