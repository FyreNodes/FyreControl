import http from '@/api/http';

export default (server: number, message: string): Promise<any> => {
    return new Promise((resolve, reject) => {
        http.post('/api/client/account/staff/request', {
            server, message,
        }).then((data) => {
            resolve(data.data || []);
        }).catch(reject);
    });
};
