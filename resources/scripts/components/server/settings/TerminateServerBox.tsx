import React, { useEffect, useState } from 'react';
import { ServerContext } from '@/state/server';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import ConfirmationModal from '@/components/elements/ConfirmationModal';
import tw from 'twin.macro';
import Button from '@/components/elements/Button';
import { faBiohazard } from '@fortawesome/free-solid-svg-icons';
import useFlash from '@/plugins/useFlash';
import terminateServer from '@/api/server/terminateServer';
import { AxiosError } from 'axios';

export default () => {
    const uuid = ServerContext.useStoreState(state => state.server.data!.uuid);
    const [ loading, setLoading ] = useState(false);
    const [ modalVisible, setModalVisible ] = useState(false);
    const { clearAndAddHttpError, clearFlashes } = useFlash();

    useEffect(() => {
        clearFlashes('settings');
    }, []);

    const terminate = () => {
        clearFlashes('settings');
        setLoading(true);

        terminateServer(uuid).then(() => {
            window.location.href = '/';
        }).catch((error: AxiosError) => {
            setModalVisible(false);
            clearAndAddHttpError({ error: error, key: 'settings' });
        }).then(() => {
            setLoading(false);
        });
    };

    return (
        <TitledGreyBox title={'Terminate Server'} icon={faBiohazard}>
            <ConfirmationModal title={'Confirm Termination'} buttonText={'Proceed'} onConfirmed={terminate} showSpinnerOverlay={loading} visible={modalVisible} onModalDismissed={() => setModalVisible(false)}>
                Your instance will be permanently terminated. This will result in permanent data loss for your instance and related services. This will also terminate your subscription.
            </ConfirmationModal>
            <p css={tw`text-sm`}>
                This will result in permanent data loss for this instance, including termination of your subscription.&nbsp;
                <strong css={tw`font-medium`}>
                    Only press this button if you are absolutely certain that you want to terminate your instance.
                </strong>
            </p>
            <div css={tw`mt-6 text-right`}>
                <Button type={'button'} color={'red'} onClick={() => setModalVisible(true)}>
                    Terminate
                </Button>
            </div>
        </TitledGreyBox>
    );
};
