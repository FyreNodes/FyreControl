import React from "react";
import tw from "twin.macro";

interface Props {
    slot: string;
}

const Adsense = ({slot}: Props) => {
    return (
        <ins className="adsbygoogle" css={tw`block`} data-ad-client="ca-pub-7944821696185059" data-ad-slot={slot} data-ad-format="auto" data-full-width-responsive="true"></ins>
    )
};

export default Adsense;
