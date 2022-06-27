import React from "react";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {IconProp} from "@fortawesome/fontawesome-svg-core";
import tw from "twin.macro";

export interface PageLabelProps {
    icon: IconProp;
    title: string;
    description: string;
}

const PageLabel: React.FC<PageLabelProps> = ({ icon, title, description, children }) => {
    return (
        <div css={tw`flex justify-between mt-2 mb-10`}>
            <div css={tw`flex`}>
                <div css={`background-color: var(--icon-color); ${tw`inline-flex mr-4 rounded-full w-14 h-14 text-center justify-center items-center`}`}>
                    <FontAwesomeIcon icon={icon} size={'lg'}/>
                </div>
                <div css={tw`justify-start`}>
                    <h1 css={tw`text-2xl`}>{title}</h1>
                    <p css={tw`text-sm`}>{description}</p>
                </div>
            </div>
            {children}
        </div>
    )
};

export default PageLabel;
