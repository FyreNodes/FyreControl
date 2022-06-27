import React from 'react';
import tw from 'twin.macro';
import { faArrowCircleDown, faArrowCircleUp, faEthernet, faHdd, faInfoCircle, faMemory, faMicrochip } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import serverStats from '@/components/functions/ServerStats';

const ServerResourceBlock = () => {
    return (
        <TitledGreyBox css={tw`break-words`} title={'Resource Usage'} icon={faInfoCircle}>
            <p css={tw`text-xs mt-2`}>
                <FontAwesomeIcon icon={faMicrochip} fixedWidth css={tw`mr-1`}/>CPU: {serverStats().cpu}%
                <span css={tw`text-neutral-500`}> of {serverStats().cpuLimit}</span>
            </p>
            <p css={tw`text-xs mt-2`}>
                <FontAwesomeIcon icon={faMemory} fixedWidth css={tw`mr-1`}/>Memory: {serverStats().memory}
                <span css={tw`text-neutral-500`}> of {serverStats().memoryLimit}</span>
            </p>
            <p css={tw`text-xs mt-2`}>
                <FontAwesomeIcon icon={faHdd} fixedWidth css={tw`mr-1`}/>Disk: {serverStats().disk}
                <span css={tw`text-neutral-500`}> of {serverStats().diskLimit}</span>
            </p>
            <p css={tw`text-xs mt-2`}>
                <FontAwesomeIcon icon={faEthernet} fixedWidth css={tw`mr-1`}/> Network:&nbsp;
                <FontAwesomeIcon icon={faArrowCircleUp} fixedWidth css={tw`mr-1`}/>{serverStats().tx}
                <FontAwesomeIcon icon={faArrowCircleDown} fixedWidth css={tw`mx-1`}/>{serverStats().rx}
            </p>
        </TitledGreyBox>
    );
};

export default ServerResourceBlock;
