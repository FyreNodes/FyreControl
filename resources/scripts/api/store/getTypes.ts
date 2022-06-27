import http from '@/api/http';

export interface Type {
    id: number;
    name: string;
    egg: number;
}

export default (): Promise<Type[]> => {
    return new Promise<Type[]>((resolve, reject) => {
        http.get('/api/client/billing/types').then((data) => resolve(data.data || [])).catch(reject);
    });
};
