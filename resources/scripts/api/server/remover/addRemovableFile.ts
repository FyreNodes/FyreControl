import http from '@/api/http';

export default (uuid: string, file: string, type: number, day: string, hour: number, minute: number): Promise<any> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/servers/${uuid}/remover/add`, {
            file, type, day, hour, minute,
        }).then((data) => {
            resolve(data.data || []);
        }).catch(reject);
    });
};
