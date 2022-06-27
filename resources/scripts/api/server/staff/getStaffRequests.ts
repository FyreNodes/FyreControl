import http from '@/api/http';
import { StaffResponse } from '@/components/server/staff/StaffContainer';

export default async (uuid: string): Promise<StaffResponse> => {
    const { data } = await http.get(`/api/client/servers/${uuid}/staff`);

    return (data.data || []);
};
