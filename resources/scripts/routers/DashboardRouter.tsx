import React from 'react';
import { NavLink, Route, Switch } from 'react-router-dom';
import AccountOverviewContainer from '@/components/dashboard/account/AccountOverviewContainer';
import NavigationBar from '@/components/NavigationBar';
import DashboardContainer from '@/components/dashboard/DashboardContainer';
import AccountApiContainer from '@/components/dashboard/account/AccountApiContainer';
import { NotFound } from '@/components/elements/ScreenBlock';
import TransitionRouter from '@/TransitionRouter';
import SubNavigation from '@/components/elements/SubNavigation';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {faCloudUploadAlt, faCog, faSignOutAlt, faLink, faKey} from '@fortawesome/free-solid-svg-icons';
import IntegrationsContainer from "@/components/dashboard/account/IntegrationsContainer";
import AccountSSHContainer from '@/components/dashboard/ssh/AccountSSHContainer';
import { useLocation } from 'react-router';

export default () => {
    const location = useLocation();

    return (
        <>
            <NavigationBar/>
            {location.pathname.startsWith('/account') &&
                <SubNavigation>
                    <div>
                        <NavLink to={'/account'} exact><FontAwesomeIcon icon={faCog}/> Settings</NavLink>
                        <NavLink to={'/account/api'}><FontAwesomeIcon icon={faCloudUploadAlt}/> API Access</NavLink>
                        <NavLink to={'/account/ssh'}><FontAwesomeIcon icon={faKey}/> SSH Keys</NavLink>
                        <NavLink to={'/account/integrations'}><FontAwesomeIcon icon={faLink}/> Integrations</NavLink>
                        <a href="/auth/logout"><FontAwesomeIcon icon={faSignOutAlt}/> Logout</a>
                    </div>
                </SubNavigation>
            }
            <TransitionRouter>
                <Switch location={location}>
                    <Route path={'/'} exact>
                        <DashboardContainer/>
                    </Route>
                    <Route path={'/account'} exact>
                        <AccountOverviewContainer/>
                    </Route>
                    <Route path={'/account/api'} exact>
                        <AccountApiContainer/>
                    </Route>
                    <Route path={'/account/ssh'} exact>
                        <AccountSSHContainer/>
                    </Route>
                    <Route path={'/account/integrations'} exact>
                        <IntegrationsContainer/>
                    </Route>
                    <Route path={'*'} component={NotFound}/>
                </Switch>
            </TransitionRouter>
        </>
    )
}
