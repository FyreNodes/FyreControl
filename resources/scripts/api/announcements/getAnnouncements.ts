import http from '@/api/http';
import { AnnouncementsResponse } from '@/components/dashboard/announcements/AnnouncementsContainer';

export default async (): Promise<AnnouncementsResponse> => {
    const { data } = await http.get('/api/client/announcements');
    return (data.data || []);
};
