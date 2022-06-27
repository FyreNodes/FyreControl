import React, {ReactNode} from "react";
import {useStoreState} from "easy-peasy";
import tw from "twin.macro";
import {Link, RouteComponentProps} from "react-router-dom";
import {loadFull} from "tsparticles";
import Particles from "react-tsparticles";

interface Props {
    children: ReactNode;
}

const AuthenticationContainer = ({ children }: Props) => {
    const appVersion: string = useStoreState(state => state.settings.data!.version);

    return (
        <>
            <Particles init={async (main) => await loadFull(main)} options={{particles: {number: {value: 40, density: {enable: true, value_area: 800}}, color: {value: '#ffffff'}, shape: {type: 'circle', stroke: {width: 0, color: '#000000'}, polygon: {nb_sides: 5}, image: {src: 'img/github.svg', width: 100, height: 100}}, opacity: {value: 0.40246529723245905, random: false, anim: {enable: false, speed: 1, opacity_min: 0.1, sync: false}}, size: {value: 1, random: true, anim: {enable: false, speed: 40, size_min: 0.1, sync: false}}, line_linked: {enable: true, distance: 150, color: '#ffffff', opacity: 0.4, width: 1}, move: {enable: true, speed: 3, direction: 'none', random: false, straight: false, out_mode: 'out', bounce: false, attract: {enable: false, rotateX: 600, rotateY: 1200}}}, interactivity: {detect_on: 'window', events: {onhover: {enable: true, mode: 'repulse'}, onclick: {enable: false, mode: 'repulse'}, resize: true}, modes: {grab: {distance: 400, line_linked: {opacity: 1}}, bubble: {distance: 400, size: 40, duration: 2, opacity: 8}, repulse: {distance: 200, duration: 0.4}, push: {particles_nb: 4}, remove: {particles_nb: 2}}}, retina_detect: true }}/>
            <div css={tw`w-5/7 my-0 mx-auto h-24 min-h-0 flex items-center justify-center md:justify-between relative`}>
                <Link to={'/auth/login'} css={tw`text-white text-4xl font-medium`}>FyreNodes</Link>
                <div css={tw`bg-blue-600 rounded text-white font-bold uppercase text-xs cursor-pointer py-3.5 px-7 tracking-wider transition-all duration-500 shadow-md hover:shadow-lg hidden md:block`}>
                    <span>Home</span>
                </div>
            </div>
            {children}
            <div css={tw`text-center no-underline text-xs mt-4`}>
                <p css={tw`text-neutral-500`}>Copyright &copy; 2021-{(new Date()).getFullYear()}&nbsp;<a rel={'nofollow noreferrer'} href={'https://fyrenodes.com'} target={'_blank'} css={tw`hover:text-neutral-200`}>FyreNodes LTD</a>. All rights reserved.</p>
                <p css={tw`text-neutral-500 mt-2`}>FyreControl - v{appVersion}</p>
            </div>
        </>
    )
}

export default AuthenticationContainer;
