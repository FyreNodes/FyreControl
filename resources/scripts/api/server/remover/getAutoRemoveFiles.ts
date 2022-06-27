import http from '@/api/http';
import { AutoRemoverResponse } from '@/components/server/remover/RemoverContainer';

export default async (uuid: string): Promise<AutoRemoverResponse> => {
    const { data } = await http.get(`/api/client/servers/${uuid}/remover`);

    return (data.data || []);
};
