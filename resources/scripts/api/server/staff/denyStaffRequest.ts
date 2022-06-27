import http from '@/api/http';

export default (uuid: string, id: number): Promise<void> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/servers/${uuid}/staff/${id}/deny`)
            .then(() => resolve())
            .catch(reject);
    });
};
