import http from "@/api/http";

export interface Construct {
    success: boolean;
    url: string;
}

export interface Unlink {
    success: boolean;
    data: string[];
}

export const construct = (): Promise<Construct> => {
    return new Promise<Construct>((resolve, reject) => {
        http.post('/auth/integrations/discord/construct').then((data) => resolve(data.data || [])).catch(reject);
    });
}

export const unlink = (): Promise<Unlink> => {
    return new Promise<Unlink>((resolve, reject) => {
        http.post('/auth/integrations/discord/unlink').then((data) => resolve(data.data || [])).catch(reject);
    });
}
