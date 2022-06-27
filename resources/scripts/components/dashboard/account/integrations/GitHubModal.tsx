import React, {useState} from 'react';
import {useStoreState} from "easy-peasy";
import {ApplicationStore} from "@/state";
import tw from "twin.macro";
import Button from "@/components/elements/Button";
import useFlash from "@/plugins/useFlash";
import {construct, unlink} from "@/api/auth/integrations/github";

export default () => {
    const user = useStoreState((state: ApplicationStore) => state.user.data!);
    const [ linked, setLinked ] = useState(!!user.githubID.length);
    const [ loading, setLoading ] = useState(false);
    const { clearFlashes, addFlash } = useFlash();

    const submit = () => {
        setLoading(true);
        clearFlashes('account:int');

        if (user.githubID.length) {
            unlink().then(data => {
                setLoading(false);
                if (!data.success) return;
                setLinked(false);
                addFlash({ title: 'FyreControl', type: 'success', key: 'account:int', message: 'You have successfully unlinked your GitHub account.' });
            })
        } else {
            construct().then(data => {
                setLoading(false);
                if (!data.success) return addFlash({ title: 'FyreControl', type: 'warning', key: 'account:int', message: 'The GitHub integration is currently disabled.' });
                window.location.href = data.url;
            });
        }
    };

    return (
        <div>
            <p css={tw`text-sm h-14`}>
                {linked ?
                    `Currently linked to: ${user.githubName} (${user.githubID})`
                    :
                    'Your FyreID is not currently linked to a GitHub account.'
                }
            </p>
            <div css={tw`mt-6 flex justify-end`}>
                <Button color={linked ? 'red' : 'green'} onClick={() => submit()} isLoading={loading}>
                    {linked ? 'Unlink' : 'Link'}
                </Button>
            </div>
        </div>
    )
};
