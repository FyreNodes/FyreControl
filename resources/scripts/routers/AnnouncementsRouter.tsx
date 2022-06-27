import React from 'react';
import { NavLink, Route, RouteComponentProps, Switch } from 'react-router-dom';
import NavigationBar from '@/components/NavigationBar';
import NotFound from '@/components/elements/ScreenBlock';
import TransitionRouter from '@/TransitionRouter';
import SubNavigation from '@/components/elements/SubNavigation';
import AnnouncementsContainer from '@/components/dashboard/announcements/AnnouncementsContainer';
import ViewAnnouncement from '@/components/dashboard/announcements/ViewAnnouncement';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faBullhorn } from '@fortawesome/free-solid-svg-icons';
import {useLocation} from "react-router";

export default () => {
    const location = useLocation()

    return (
        <>
            <NavigationBar/>
            {location.pathname.startsWith('/announcements') &&
                <SubNavigation>
                    <div>
                        <NavLink to={'/announcements'} exact><FontAwesomeIcon icon={faBullhorn}/> Announcements</NavLink>
                    </div>
                </SubNavigation>
            }
            <TransitionRouter>
                <Switch location={location}>
                    <Route path={'/announcements'} exact>
                        <AnnouncementsContainer/>
                    </Route>
                    <Route path={'/announcements/:id'} exact>
                        <ViewAnnouncement/>
                    </Route>
                    <Route path={'*'} component={NotFound}/>
                </Switch>
            </TransitionRouter>
        </>
    )
};
