import http from '@/api/http';
import { ViewAnnouncementResponse } from '@/components/dashboard/announcements/ViewAnnouncement';

export default async (id: string): Promise<ViewAnnouncementResponse> => {
    const { data } = await http.get(`/api/client/announcements/${id}`);
    return (data.data || []);
};
