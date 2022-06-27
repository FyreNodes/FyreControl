import http from '@/api/http';

export interface RegisterResponse {
    complete: boolean;
    intended?: string;
    confirmationToken?: string;
}

export interface RegisterData {
    username: string;
    email: string;
    NameFirst: string;
    NameLast: string;
    password: string;
    recaptchaData?: string | null;
}

export default ({ username, email, NameFirst, NameLast, password, recaptchaData }: RegisterData): Promise<RegisterResponse> => {
    return new Promise((resolve, reject) => {
        http.post('/auth/register', {
            username: username,
            email: email,
            name_first: NameFirst,
            name_last: NameLast,
            password: password,
            'g-recaptcha-response': recaptchaData,
        })
            .then(response => {
                if (!(response.data instanceof Object)) {
                    return reject(new Error('An error occurred while processing the register request.'));
                }

                return resolve({
                    complete: response.data.data.complete,
                    intended: response.data.data.intended || undefined,
                    confirmationToken: response.data.data.confirmation_token || undefined,
                });
            })
            .catch(reject);
    });
};
