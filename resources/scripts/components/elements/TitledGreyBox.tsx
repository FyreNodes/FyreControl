import React, { memo } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconProp } from '@fortawesome/fontawesome-svg-core';
import tw from 'twin.macro';
import isEqual from 'react-fast-compare';

interface Props {
    icon?: IconProp;
    title: string | React.ReactNode;
    hideTitle?: boolean;
    noPadding?: boolean;
    customBorder?: boolean;
    className?: string;
    children: React.ReactNode;
}

const TitledGreyBox = ({ icon, title, children, className, hideTitle, noPadding, customBorder }: Props) => (
    <div css={`${customBorder && '--tw-shadow: 0 0px 4px 1px rgb(28, 177, 223),0 2px 4px -1px rgb(28, 177, 223) !important; background-color: transparent !important;'} ${tw`rounded shadow-md`}`} className={className}>
        {!hideTitle &&
            <div css={`background-color: var(--header-color); ${tw`rounded-t p-3`}`}>
                {typeof title === 'string' ?
                    <p css={tw`text-sm uppercase`}>
                        {icon && <FontAwesomeIcon icon={icon} css={tw`mr-2 text-neutral-300`}/>}{title}
                    </p>
                    :
                    title
                }
            </div>
        }
        <div css={`background-color: var(--primary-color); ${noPadding ? tw`p-0` : tw`p-3`}`}>
            {children}
        </div>
    </div>
);

export default memo(TitledGreyBox, isEqual);
