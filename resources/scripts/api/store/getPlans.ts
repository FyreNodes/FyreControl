import http from '@/api/http';

export interface Plan {
    id: number;
    name: string;
    description: string;
    image: string;
    price: string;
}

export default (): Promise<Plan[]> => {
    return new Promise<Plan[]>((resolve, reject) => {
        http.get('/api/client/billing/plans').then((data) => resolve(data.data || [])).catch(reject);
    });
};
