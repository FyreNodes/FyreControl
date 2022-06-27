import React, { useState, useEffect } from 'react';
import { Link, NavLink } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCogs, faLayerGroup, faUserCog, faBullhorn, faBook, faUsersCog, faSignOutAlt, faCaretDown, faStore } from '@fortawesome/free-solid-svg-icons';
import { useStoreState } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import SearchContainer from '@/components/dashboard/search/SearchContainer';
import tw from 'twin.macro';
import http from '@/api/http';
import getUserRole from '@/api/userRole';
import toMD5 from '@/components/functions/md5';
import { NavigationStyle, RightNavigationStyle, Dropdown, DropdownUser, DropdownContent } from '@/assets/styles/NavigationStyle';

export default () => {
    const name = useStoreState((state: ApplicationStore) => state.settings.data!.name);
    const staff = useStoreState((state: ApplicationStore) => state.user.data!.staff);
    const userdata = useStoreState((state: ApplicationStore) => state.user.data!);
    const [ userRoleData, setUserRoleData ] = useState(false);
    const [ isLoggingOut, setIsLoggingOut ] = useState(false);
    const [ isHover, setIsHover ] = useState(false);

    async function onTriggerLogout () {
        setIsLoggingOut(true);
        await http.post('/auth/logout').finally(() => {
            // @ts-ignore
            window.location = '/';
        });
    }

    useEffect(() => {
        async function getUserRoleData () {
            const user = await getUserRole();
            if (user.role > 0) {
                setUserRoleData(true);
            } else {
                setUserRoleData(false);
            }
        }
        getUserRoleData().then();
    }, []);

    return (
        <NavigationStyle>
            <SpinnerOverlay visible={isLoggingOut}/>
            <div css={tw`mx-auto w-full flex items-center`} style={{ maxWidth: '1200px', height: '3.5rem' }}>
                <div id={'logo'}>
                    <Link to={'/'}>
                        {name}
                    </Link>
                </div>
                <RightNavigationStyle>
                    <SearchContainer/>
                    <NavLink to={'/'} exact>
                        <FontAwesomeIcon icon={faLayerGroup}/>
                    </NavLink>
                    <NavLink to={'/announcements'}>
                        <FontAwesomeIcon icon={faBullhorn}/>
                    </NavLink>
                    <NavLink to={'/store'}>
                        <FontAwesomeIcon icon={faStore}/>
                    </NavLink>
                    <Dropdown onMouseEnter={() => setIsHover(true)} onMouseLeave={() => setIsHover(false)}>
                        <DropdownUser>
                            <img css={tw`flex-shrink-0 object-cover w-7 h-7 rounded-xl inline-block`} src={`https://gravatar.com/avatar/${toMD5(userdata.email.toString().toLowerCase())}?s=160`} alt="user-image"/>
                            &nbsp;&nbsp;
                            <div css={tw`hidden sm:inline-block`}>
                                {userdata.name_first}
                                <FontAwesomeIcon icon={faCaretDown}/>
                            </div>
                        </DropdownUser>
                        {isHover &&
                            <DropdownContent>
                                <NavLink to={'/account'}>
                                    <FontAwesomeIcon fixedWidth icon={faUserCog}/> Account
                                </NavLink>
                                <NavLink to={'/knowledgebase'}>
                                    <FontAwesomeIcon fixedWidth icon={faBook}/> Information
                                </NavLink>
                                {staff === 1 &&
                                    <NavLink to={'/staff'}>
                                        <FontAwesomeIcon fixedWidth icon={faUsersCog}/> Staff
                                    </NavLink>
                                }
                                {userRoleData &&
                                    <a href="/admin"><FontAwesomeIcon fixedWidth icon={faCogs}/> Admin</a>
                                }
                                <button onClick={onTriggerLogout}><FontAwesomeIcon fixedWidth icon={faSignOutAlt}/> Logout</button>
                            </DropdownContent>
                        }
                    </Dropdown>
                    {/* <div className="user-settings">
                        <div className="dropdown-user">
                            <div className="user-info" css={tw`cursor-pointer text-white mr-1.5 ml-3`}>
                                <img css={tw`flex-shrink-0 object-cover inline-block`} className="user-img" src={'https://www.gravatar.com/avatar/' + toMD5(userdata.email.toString().toLowerCase()) + '?s=160'} alt=""/>
                                &nbsp;&nbsp;
                                <div css={tw`hidden sm:inline-block`}>
                                    {userdata.name_first}
                                    <FontAwesomeIcon icon={faCaretDown}/>
                                </div>
                            </div>
                            <div className="dropdown-content">
                            </div>
                        </div>
                    </div> */}
                </RightNavigationStyle>
            </div>
        </NavigationStyle>
    );
};
